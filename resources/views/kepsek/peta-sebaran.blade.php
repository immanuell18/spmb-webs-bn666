@extends('layouts.admin')

@section('title', 'Peta Sebaran Domisili - Kepala Sekolah')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">🗺️ Peta Sebaran Domisili Calon Siswa</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="toggleHeatmap">
                            <i class="ti ti-flame"></i> Heatmap
                        </button>
                        <button class="btn btn-outline-success btn-sm" id="toggleClusters">
                            <i class="ti ti-circles"></i> Cluster
                        </button>
                        <button class="btn btn-outline-info btn-sm" id="refreshMap">
                            <i class="ti ti-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Controls -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="filterJurusan">
                                <option value="">Semua Jurusan</option>
                                @foreach($jurusan as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterGelombang">
                                <option value="">Semua Gelombang</option>
                                @foreach($gelombang as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="SUBMIT">Menunggu Verifikasi</option>
                                <option value="ADM_PASS">Berkas Disetujui</option>
                                <option value="PAID">Sudah Bayar</option>
                                <option value="ADM_REJECT">Berkas Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" id="applyFilters">
                                <i class="ti ti-filter"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div id="map" style="height: 500px; border-radius: 8px;"></div>

                    <!-- Statistics Panel -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4 id="totalPendaftar">0</h4>
                                    <p class="mb-0">Total Pendaftar</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4 id="sudahBayar">0</h4>
                                    <p class="mb-0">Sudah Bayar</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4 id="terverifikasi">0</h4>
                                    <p class="mb-0">Terverifikasi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4 id="menunggu">0</h4>
                                    <p class="mb-0">Menunggu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Area Statistics -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📊 Statistik Sebaran per Wilayah</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="areaStatsTable">
                            <thead>
                                <tr>
                                    <th>Wilayah</th>
                                    <th>Total Pendaftar</th>
                                    <th>Sudah Bayar</th>
                                    <th>Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script src="{{ asset('js/map.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map for Kepala Sekolah (read-only mode)
    const mapInstance = new SPMBMap('map', {
        center: [-6.2088, 106.8456], // Jakarta
        zoom: 10,
        readOnly: true // Kepala sekolah hanya bisa melihat
    });

    // Load initial data
    mapInstance.loadMarkers().then(data => {
        if (data && data.statistics) {
            updateStatistics(data.statistics);
        }
    });
    loadAreaStatistics();

    // Filter controls
    document.getElementById('applyFilters').addEventListener('click', function() {
        const filters = {
            jurusan_id: document.getElementById('filterJurusan').value,
            gelombang_id: document.getElementById('filterGelombang').value,
            status: document.getElementById('filterStatus').value
        };
        
        mapInstance.loadMarkers(filters).then(data => {
            if (data && data.statistics) {
                updateStatistics(data.statistics);
            }
        });
        loadAreaStatistics(filters);
    });

    // Toggle controls
    document.getElementById('toggleHeatmap').addEventListener('click', function() {
        mapInstance.toggleHeatmap();
        this.classList.toggle('active');
    });

    document.getElementById('toggleClusters').addEventListener('click', function() {
        mapInstance.toggleClusters();
        this.classList.toggle('active');
    });

    document.getElementById('refreshMap').addEventListener('click', function() {
        mapInstance.loadMarkers().then(data => {
            if (data && data.statistics) {
                updateStatistics(data.statistics);
            }
        });
        loadAreaStatistics();
    });

    function updateStatistics(stats) {
        document.getElementById('totalPendaftar').textContent = stats.total || 0;
        document.getElementById('sudahBayar').textContent = stats.by_status?.PAID || 0;
        document.getElementById('terverifikasi').textContent = stats.by_status?.ADM_PASS || 0;
        document.getElementById('menunggu').textContent = stats.by_status?.SUBMIT || 0;
    }

    function loadAreaStatistics(filters = {}) {
        fetch('/api/map/area-statistics?' + new URLSearchParams(filters))
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#areaStatsTable tbody');
                tbody.innerHTML = '';
                
                if (data.top_areas && data.top_areas.length > 0) {
                    data.top_areas.forEach(area => {
                        const percentage = area.total_pendaftar > 0 ? 
                            ((area.sudah_bayar / area.total_pendaftar) * 100).toFixed(1) : 0;
                        
                        tbody.innerHTML += `
                            <tr>
                                <td>${area.area}</td>
                                <td>${area.total_pendaftar}</td>
                                <td>${area.sudah_bayar}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: ${percentage}%" 
                                             aria-valuenow="${percentage}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            ${percentage}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Belum ada data</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading area statistics:', error);
                const tbody = document.querySelector('#areaStatsTable tbody');
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Error loading data</td></tr>';
            });
    }

    // Auto refresh every 30 seconds
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            mapInstance.loadMarkers().then(data => {
                if (data && data.statistics) {
                    updateStatistics(data.statistics);
                }
            });
            loadAreaStatistics();
        }
    }, 30000);
});
</script>

<style>
.btn.active {
    background-color: var(--bs-primary);
    color: white;
}

.progress {
    background-color: #e9ecef;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

#map {
    border: 2px solid #dee2e6;
}

.leaflet-popup-content {
    margin: 8px 12px;
    line-height: 1.4;
}

.popup-content h6 {
    margin-bottom: 5px;
    color: #495057;
}

.popup-content p {
    margin-bottom: 3px;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection