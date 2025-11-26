<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\Jurusan;
use App\Models\Gelombang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $jurusan = Jurusan::all();
        $gelombang = Gelombang::all();
        
        // Detect user role and return appropriate view
        if (auth()->user()->role === 'kepsek') {
            return view('kepsek.peta-sebaran', compact('jurusan', 'gelombang'));
        }
        
        return view('admin.peta-sebaran', compact('jurusan', 'gelombang'));
    }

    public function getMapData(Request $request)
    {
        $query = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa'])
                         ->whereHas('dataSiswa', function($q) {
                             $q->whereNotNull('lat')->whereNotNull('lng');
                         });

        // Filters
        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('gelombang_id')) {
            $query->where('gelombang_id', $request->gelombang_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pendaftar = $query->get();

        $markers = $pendaftar->map(function ($p) {
            return [
                'id' => $p->id,
                'lat' => (float) $p->dataSiswa->lat,
                'lng' => (float) $p->dataSiswa->lng,
                'nama' => $p->nama,
                'no_pendaftaran' => $p->no_pendaftaran,
                'jurusan' => $p->jurusan->nama,
                'gelombang' => $p->gelombang->nama,
                'status' => $p->status,
                'alamat' => $p->dataSiswa->alamat,
                'popup_content' => $this->generatePopupContent($p)
            ];
        });

        return response()->json([
            'markers' => $markers,
            'total' => $markers->count(),
            'statistics' => $this->getStatistics($pendaftar)
        ]);
    }

    public function getHeatmapData(Request $request)
    {
        $query = Pendaftar::with(['dataSiswa'])
                         ->whereHas('dataSiswa', function($q) {
                             $q->whereNotNull('lat')->whereNotNull('lng');
                         });

        // Apply filters
        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('gelombang_id')) {
            $query->where('gelombang_id', $request->gelombang_id);
        }

        $pendaftar = $query->get();

        $heatmapData = $pendaftar->map(function ($p) {
            return [
                (float) $p->dataSiswa->lat,
                (float) $p->dataSiswa->lng,
                1 // intensity
            ];
        });

        return response()->json($heatmapData);
    }

    public function getClusterData(Request $request)
    {
        // Group by approximate location (0.01 degree precision)
        $clusters = DB::table('pendaftar')
            ->join('pendaftar_data_siswa', 'pendaftar.id', '=', 'pendaftar_data_siswa.pendaftar_id')
            ->join('jurusan', 'pendaftar.jurusan_id', '=', 'jurusan.id')
            ->whereNotNull('pendaftar_data_siswa.lat')
            ->whereNotNull('pendaftar_data_siswa.lng')
            ->select(
                DB::raw('ROUND(lat, 2) as cluster_lat'),
                DB::raw('ROUND(lng, 2) as cluster_lng'),
                DB::raw('COUNT(*) as count'),
                DB::raw('GROUP_CONCAT(pendaftar.nama) as names'),
                DB::raw('GROUP_CONCAT(jurusan.nama) as jurusan_list')
            )
            ->groupBy('cluster_lat', 'cluster_lng')
            ->having('count', '>', 1)
            ->get();

        return response()->json($clusters);
    }

    public function geocodeAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string'
        ]);

        try {
            // Using OpenStreetMap Nominatim (free)
            $response = Http::get('https://nominatim.openstreetmap.org/search', [
                'q' => $request->address . ', Indonesia',
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => 'id'
            ]);

            if ($response->successful() && count($response->json()) > 0) {
                $data = $response->json()[0];
                return response()->json([
                    'success' => true,
                    'lat' => (float) $data['lat'],
                    'lng' => (float) $data['lon'],
                    'display_name' => $data['display_name']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error geocoding: ' . $e->getMessage()
            ]);
        }
    }

    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);

        try {
            $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $request->lat,
                'lon' => $request->lng,
                'format' => 'json',
                'zoom' => 18,
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'address' => $data['display_name'] ?? '',
                    'details' => $data['address'] ?? []
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Koordinat tidak valid'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reverse geocoding: ' . $e->getMessage()
            ]);
        }
    }

    public function getAreaStatistics(Request $request)
    {
        $filters = $request->only(['jurusan_id', 'gelombang_id', 'status']);
        $mapService = new \App\Services\MapService();
        
        $stats = $mapService->getAreaStatistics($filters);
        $topAreas = $mapService->getTopAreas(5, $filters);
        
        return response()->json([
            'statistics' => $stats,
            'top_areas' => $topAreas
        ]);
    }
    
    public function exportMapData(Request $request)
    {
        $filters = $request->only(['jurusan_id', 'gelombang_id', 'status']);
        $mapService = new \App\Services\MapService();
        
        $data = $mapService->generateMapExport($filters);
        
        // Return as JSON for now - could be enhanced to generate PDF/Excel
        return response()->json($data);
    }

    private function generatePopupContent($pendaftar)
    {
        $statusBadge = match($pendaftar->status) {
            'SUBMIT' => '<span class="badge badge-warning">Menunggu Verifikasi</span>',
            'ADM_PASS' => '<span class="badge badge-info">Berkas Disetujui</span>',
            'ADM_REJECT' => '<span class="badge badge-danger">Berkas Ditolak</span>',
            'PAID' => '<span class="badge badge-success">Sudah Bayar</span>',
            default => '<span class="badge badge-secondary">Unknown</span>'
        };

        return "
            <div class='popup-content'>
                <h6><strong>{$pendaftar->nama}</strong></h6>
                <p><small>No: {$pendaftar->no_pendaftaran}</small></p>
                <p><strong>Jurusan:</strong> {$pendaftar->jurusan->nama}</p>
                <p><strong>Gelombang:</strong> {$pendaftar->gelombang->nama}</p>
                <p><strong>Status:</strong> {$statusBadge}</p>
                <p><strong>Alamat:</strong> {$pendaftar->dataSiswa->alamat}</p>
            </div>
        ";
    }

    private function getStatistics($pendaftar)
    {
        return [
            'total' => $pendaftar->count(),
            'by_jurusan' => $pendaftar->groupBy('jurusan.nama')->map->count(),
            'by_status' => $pendaftar->groupBy('status')->map->count(),
            'by_gelombang' => $pendaftar->groupBy('gelombang.nama')->map->count()
        ];
    }
}