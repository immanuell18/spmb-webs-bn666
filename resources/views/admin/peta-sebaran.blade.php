@extends('layouts.admin')

@section('title', 'Peta Sebaran Pendaftar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">🗺️ Peta Sebaran Pendaftar</h4>
                    <div>
                        <button class="btn btn-info btn-sm" onclick="toggleHeatmap()">
                            <i class="fas fa-fire"></i> Toggle Heatmap
                        </button>
                        <button class="btn btn-success btn-sm" onclick="exportMap()">
                            <i class="fas fa-download"></i> Export Map
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="mapFilters" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Jurusan</label>
                            <select class="form-select" name="jurusan_id" id="filterJurusan">
                                <option value="">Semua Jurusan</option>
                                @foreach($jurusan as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Gelombang</label>
                            <select class="form-select" name="gelombang_id" id="filterGelombang">
                                <option value="">Semua Gelombang</option>
                                @foreach($gelombang as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }} - {{ $g->tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="SUBMIT">Menunggu Verifikasi</option>
                                <option value="ADM_PASS">Berkas Disetujui</option>
                                <option value="ADM_REJECT">Berkas Ditolak</option>
                                <option value="PAID">Sudah Bayar</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary me-2" onclick="applyFilters()">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4" id="statisticsCards">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 id="totalPendaftar">0</h5>
                    <small>Total Pendaftar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 id="sudahBayar">0</h5>
                    <small>Sudah Bayar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 id="menungguVerifikasi">0</h5>
                    <small>Menunggu Verifikasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 id="berkasRejected">0</h5>
                    <small>Berkas Ditolak</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Peta Interaktif</h5>
                </div>
                <div class="card-body p-0">
                    <div id="mainMap" style="height: 600px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📊 Statistik Sebaran</h5>
                </div>
                <div class="card-body">
                    <!-- By Jurusan -->
                    <div class="mb-4">
                        <h6>Berdasarkan Jurusan</h6>
                        <div id="jurusanStats"></div>
                    </div>
                    
                    <!-- By Status -->
                    <div class="mb-4">
                        <h6>Berdasarkan Status</h6>
                        <div id="statusStats"></div>
                    </div>
                    
                    <!-- By Area -->
                    <div class="mb-4">
                        <h6>Area Terpadat</h6>
                        <div id="areaStats">
                            <small class="text-muted">Loading...</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">🎨 Legend</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div style="width: 20px; height: 20px; background-color: #ffc107; border-radius: 50%; margin-right: 10px;"></div>
                        <small>Menunggu Verifikasi</small>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div style="width: 20px; height: 20px; background-color: #17a2b8; border-radius: 50%; margin-right: 10px;"></div>
                        <small>Berkas Disetujui</small>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div style="width: 20px; height: 20px; background-color: #dc3545; border-radius: 50%; margin-right: 10px;"></div>
                        <small>Berkas Ditolak</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div style="width: 20px; height: 20px; background-color: #28a745; border-radius: 50%; margin-right: 10px;"></div>
                        <small>Sudah Bayar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
let adminMap = null;

document.addEventListener('DOMContentLoaded', function() {
    initAdminMap();
    loadAreaStatistics();
});

function initAdminMap() {
    // Initialize map
    adminMap = L.map('mainMap').setView([-6.2088, 106.8456], 10);

    // Add tile layers
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri'
    });

    osmLayer.addTo(adminMap);

    // Layer control
    const baseLayers = {
        "OpenStreetMap": osmLayer,
        "Satellite": satelliteLayer
    };
    L.control.layers(baseLayers).addTo(adminMap);

    // Initialize marker cluster
    adminMap.markerCluster = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 50
    });
    adminMap.addLayer(adminMap.markerCluster);

    // Load initial data
    loadMapData();
}

async function loadMapData(filters = {}) {
    try {
        const params = new URLSearchParams(filters);
        const response = await fetch(`/api/map/data?${params}`);
        const data = await response.json();

        // Clear existing markers
        adminMap.markerCluster.clearLayers();

        // Add new markers
        data.markers.forEach(marker => {
            const leafletMarker = L.marker([marker.lat, marker.lng])
                .bindPopup(marker.popup_content);

            // Custom icon based on status
            const iconColor = getStatusColor(marker.status);
            leafletMarker.setIcon(createCustomIcon(iconColor));

            adminMap.markerCluster.addLayer(leafletMarker);
        });

        // Update statistics
        updateStatistics(data.statistics);

        // Fit bounds if markers exist
        if (data.markers.length > 0) {
            setTimeout(() => {
                adminMap.fitBounds(adminMap.markerCluster.getBounds().pad(0.1));
            }, 100);
        }

    } catch (error) {
        console.error('Error loading map data:', error);
    }
}

function createCustomIcon(color) {
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });
}

function getStatusColor(status) {
    const colors = {
        'SUBMIT': '#ffc107',
        'ADM_PASS': '#17a2b8',
        'ADM_REJECT': '#dc3545',
        'PAID': '#28a745'
    };
    return colors[status] || '#6c757d';
}

function updateStatistics(stats) {
    document.getElementById('totalPendaftar').textContent = stats.total || 0;
    
    // Update status cards
    const statusCounts = stats.by_status || {};
    document.getElementById('sudahBayar').textContent = statusCounts.PAID || 0;
    document.getElementById('menungguVerifikasi').textContent = statusCounts.SUBMIT || 0;
    document.getElementById('berkasRejected').textContent = statusCounts.ADM_REJECT || 0;

    // Update jurusan stats
    const jurusanStats = document.getElementById('jurusanStats');
    jurusanStats.innerHTML = '';
    if (stats.by_jurusan) {
        Object.entries(stats.by_jurusan).forEach(([jurusan, count]) => {
            jurusanStats.innerHTML += `
                <div class="d-flex justify-content-between mb-1">
                    <small>${jurusan}</small>
                    <small><strong>${count}</strong></small>
                </div>
            `;
        });
    }

    // Update status stats
    const statusStatsDiv = document.getElementById('statusStats');
    statusStatsDiv.innerHTML = '';
    if (stats.by_status) {
        Object.entries(stats.by_status).forEach(([status, count]) => {
            const statusName = getStatusName(status);
            const color = getStatusColor(status);
            statusStatsDiv.innerHTML += `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center">
                        <div style="width: 12px; height: 12px; background-color: ${color}; border-radius: 50%; margin-right: 8px;"></div>
                        <small>${statusName}</small>
                    </div>
                    <small><strong>${count}</strong></small>
                </div>
            `;
        });
    }
}

function getStatusName(status) {
    const names = {
        'SUBMIT': 'Menunggu Verifikasi',
        'ADM_PASS': 'Berkas Disetujui',
        'ADM_REJECT': 'Berkas Ditolak',
        'PAID': 'Sudah Bayar'
    };
    return names[status] || status;
}

async function loadAreaStatistics() {
    try {
        const response = await fetch('/api/map/area-statistics');
        const data = await response.json();
        
        const areaStats = document.getElementById('areaStats');
        areaStats.innerHTML = '';
        
        data.slice(0, 5).forEach((area, index) => {
            areaStats.innerHTML += `
                <div class="d-flex justify-content-between mb-1">
                    <small>${index + 1}. ${area.area}</small>
                    <small><strong>${area.total_pendaftar}</strong></small>
                </div>
            `;
        });
    } catch (error) {
        console.error('Error loading area statistics:', error);
    }
}

function applyFilters() {
    const formData = new FormData(document.getElementById('mapFilters'));
    const filters = Object.fromEntries(formData.entries());
    loadMapData(filters);
}

function resetFilters() {
    document.getElementById('mapFilters').reset();
    loadMapData();
}

async function toggleHeatmap() {
    try {
        if (adminMap.heatmapLayer) {
            if (adminMap.hasLayer(adminMap.heatmapLayer)) {
                adminMap.removeLayer(adminMap.heatmapLayer);
            } else {
                adminMap.addLayer(adminMap.heatmapLayer);
            }
        } else {
            // Load heatmap data
            const response = await fetch('/api/map/heatmap');
            const data = await response.json();
            
            adminMap.heatmapLayer = L.heatLayer(data, {
                radius: 25,
                blur: 15,
                maxZoom: 17
            });
            
            adminMap.addLayer(adminMap.heatmapLayer);
        }
    } catch (error) {
        console.error('Error toggling heatmap:', error);
    }
}

function exportMap() {
    // Simple implementation - can be enhanced with html2canvas
    alert('Export functionality will be implemented with html2canvas library');
}
</script>
@endsection