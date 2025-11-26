@extends('layouts.admin')

@section('title', 'Peta Sebaran Pendaftar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">🗺️ Peta Sebaran Pendaftar</h1>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <select id="filterJurusan" class="form-select">
                                <option value="">Semua Jurusan</option>
                                @foreach($jurusan as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filterGelombang" class="form-select">
                                <option value="">Semua Gelombang</option>
                                @foreach($gelombang as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filterStatus" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="SUBMIT">Submit</option>
                                <option value="ADM_PASS">Lulus Administrasi</option>
                                <option value="ADM_REJECT">Ditolak</option>
                                <option value="PAID">Sudah Bayar</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group w-100">
                                <button class="btn btn-primary" onclick="toggleHeatmap()">
                                    <i class="fas fa-fire"></i> Heatmap
                                </button>
                                <button class="btn btn-success" onclick="exportMap()">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map and Statistics -->
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Peta Sebaran</h5>
                    <div>
                        <span class="badge bg-primary" id="totalMarkers">0 Pendaftar</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 500px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <!-- Statistics -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">📊 Statistik</h6>
                </div>
                <div class="card-body">
                    <div id="statisticsContent">
                        <div class="text-center text-muted">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Areas -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">🏆 Top 5 Wilayah</h6>
                </div>
                <div class="card-body">
                    <div id="topAreasContent">
                        <div class="text-center text-muted">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />

<script>
let map;
let markersLayer;
let heatmapLayer;
let clusterGroup;
let isHeatmapVisible = false;

// Initialize map
function initMap() {
    map = L.map('map').setView([-6.2088, 106.8456], 10);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Initialize cluster group
    clusterGroup = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 50
    });
    
    loadMapData();
}

// Load map data with filters
function loadMapData() {
    const filters = {
        jurusan_id: document.getElementById('filterJurusan').value,
        gelombang_id: document.getElementById('filterGelombang').value,
        status: document.getElementById('filterStatus').value
    };
    
    const params = new URLSearchParams(filters);
    
    fetch(`/api/map/data?${params}`)
        .then(response => response.json())
        .then(data => {
            updateMap(data.markers);
            updateStatistics(data.statistics);
            document.getElementById('totalMarkers').textContent = `${data.total} Pendaftar`;
        })
        .catch(error => {
            console.error('Error loading map data:', error);
        });
}

// Update map markers
function updateMap(markers) {
    // Clear existing markers
    if (markersLayer) {
        map.removeLayer(markersLayer);
    }
    clusterGroup.clearLayers();
    
    if (markers.length === 0) {
        return;
    }
    
    // Add markers to cluster group
    markers.forEach(marker => {
        const icon = getMarkerIcon(marker.status);
        const leafletMarker = L.marker([marker.lat, marker.lng], { icon })
            .bindPopup(marker.popup_content);
        
        clusterGroup.addLayer(leafletMarker);
    });
    
    map.addLayer(clusterGroup);
    
    // Fit map to markers bounds
    if (markers.length > 0) {
        const group = new L.featureGroup(clusterGroup.getLayers());
        map.fitBounds(group.getBounds().pad(0.1));
    }
}

// Get marker icon based on status
function getMarkerIcon(status) {
    const colors = {
        'SUBMIT': 'orange',
        'ADM_PASS': 'blue',
        'ADM_REJECT': 'red',
        'PAID': 'green'
    };
    
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${colors[status] || 'gray'}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>`,
        iconSize: [16, 16],
        iconAnchor: [8, 8]
    });
}

// Toggle heatmap
function toggleHeatmap() {
    if (isHeatmapVisible) {
        if (heatmapLayer) {
            map.removeLayer(heatmapLayer);
        }
        map.addLayer(clusterGroup);
        isHeatmapVisible = false;
    } else {
        loadHeatmapData();
    }
}

// Load heatmap data
function loadHeatmapData() {
    const filters = {
        jurusan_id: document.getElementById('filterJurusan').value,
        gelombang_id: document.getElementById('filterGelombang').value,
        status: document.getElementById('filterStatus').value
    };
    
    const params = new URLSearchParams(filters);
    
    fetch(`/api/map/heatmap?${params}`)
        .then(response => response.json())
        .then(data => {
            if (heatmapLayer) {
                map.removeLayer(heatmapLayer);
            }
            
            map.removeLayer(clusterGroup);
            
            heatmapLayer = L.heatLayer(data, {
                radius: 25,
                blur: 15,
                maxZoom: 17
            }).addTo(map);
            
            isHeatmapVisible = true;
        })
        .catch(error => {
            console.error('Error loading heatmap data:', error);
        });
}

// Update statistics
function updateStatistics(stats) {
    let html = '';
    
    if (stats.by_jurusan) {
        html += '<h6>Per Jurusan:</h6>';
        Object.entries(stats.by_jurusan).forEach(([jurusan, count]) => {
            html += `<div class="d-flex justify-content-between mb-1">
                <small>${jurusan}</small>
                <span class="badge bg-primary">${count}</span>
            </div>`;
        });
    }
    
    if (stats.by_status) {
        html += '<h6 class="mt-3">Per Status:</h6>';
        Object.entries(stats.by_status).forEach(([status, count]) => {
            const badgeClass = {
                'SUBMIT': 'warning',
                'ADM_PASS': 'info',
                'ADM_REJECT': 'danger',
                'PAID': 'success'
            }[status] || 'secondary';
            
            html += `<div class="d-flex justify-content-between mb-1">
                <small>${status}</small>
                <span class="badge bg-${badgeClass}">${count}</span>
            </div>`;
        });
    }
    
    document.getElementById('statisticsContent').innerHTML = html;
}

// Export map
function exportMap() {
    // Simple implementation - could be enhanced with actual image export
    const filters = {
        jurusan_id: document.getElementById('filterJurusan').value,
        gelombang_id: document.getElementById('filterGelombang').value,
        status: document.getElementById('filterStatus').value
    };
    
    const params = new URLSearchParams(filters);
    window.open(`/api/map/area-statistics?${params}`, '_blank');
}

// Event listeners
document.getElementById('filterJurusan').addEventListener('change', loadMapData);
document.getElementById('filterGelombang').addEventListener('change', loadMapData);
document.getElementById('filterStatus').addEventListener('change', loadMapData);

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', initMap);
</script>

<style>
.custom-marker {
    background: transparent;
    border: none;
}

.leaflet-popup-content {
    margin: 8px 12px;
    line-height: 1.4;
}

.badge {
    font-size: 0.7em;
}

.popup-content h6 {
    margin-bottom: 5px;
    color: #333;
}

.popup-content p {
    margin-bottom: 3px;
    font-size: 0.9em;
}
</style>
@endsection