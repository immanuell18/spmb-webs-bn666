<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurusan;
use App\Models\Gelombang;
use App\Models\Wilayah;
use App\Models\Pendaftar;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request = null)
    {
        $gelombangAktif = Gelombang::getActive();
        $selectedGelombang = $request ? $request->get('gelombang') : null;
        
        // Base query dengan filter gelombang jika dipilih
        $baseQuery = Pendaftar::query();
        if ($selectedGelombang) {
            $baseQuery->where('gelombang_id', $selectedGelombang);
        }
        
        // KPI Utama
        $totalPendaftar = (clone $baseQuery)->count();
        $pendaftarBaru = (clone $baseQuery)->whereDate('created_at', today())->count();
        $sudahVerifikasi = (clone $baseQuery)->where('status', 'ADM_PASS')->count();
        $sudahBayar = (clone $baseQuery)->where('status', 'PAID')->count();
        $menungguVerifikasi = (clone $baseQuery)->where('status', 'SUBMIT')->count();
        $ditolak = (clone $baseQuery)->where('status', 'ADM_REJECT')->count();
        
        // Tren pendaftaran harian dengan filter gelombang
        $trenQuery = Pendaftar::selectRaw('DATE(created_at) as tanggal, COUNT(*) as jumlah')
            ->where('created_at', '>=', now()->subDays(30));
        if ($selectedGelombang) {
            $trenQuery->where('gelombang_id', $selectedGelombang);
        }
        $trenHarian = $trenQuery->groupBy('tanggal')->orderBy('tanggal')->get();
        
        // If no data, create empty data for chart
        if ($trenHarian->isEmpty()) {
            $trenHarian = collect();
            for ($i = 6; $i >= 0; $i--) {
                $trenHarian->push((object)[
                    'tanggal' => now()->subDays($i)->format('Y-m-d'),
                    'jumlah' => 0
                ]);
            }
        }
        
        // Statistik per jurusan
        $statistikJurusan = Jurusan::withCount('pendaftar')
            ->with(['pendaftar' => function($q) {
                $q->selectRaw('jurusan_id, status, COUNT(*) as jumlah')
                  ->groupBy('jurusan_id', 'status');
            }])
            ->get();
        
        // Statistik per gelombang
        $statistikGelombang = Gelombang::withCount('pendaftar')
            ->with(['pendaftar' => function($q) {
                $q->selectRaw('gelombang_id, status, COUNT(*) as jumlah')
                  ->groupBy('gelombang_id', 'status');
            }])
            ->get();
        
        // Data untuk peta sebaran (koordinat real)
        $sebaranKoordinat = Pendaftar::join('pendaftar_data_siswa', 'pendaftar.id', '=', 'pendaftar_data_siswa.pendaftar_id')
            ->whereNotNull('pendaftar_data_siswa.lat')
            ->whereNotNull('pendaftar_data_siswa.lng')
            ->select('pendaftar_data_siswa.lat', 'pendaftar_data_siswa.lng', 'pendaftar.nama', 'jurusan.nama as jurusan')
            ->join('jurusan', 'pendaftar.jurusan_id', '=', 'jurusan.id')
            ->get();
        
        // Top 5 asal sekolah
        $topSekolah = Pendaftar::join('pendaftar_asal_sekolah', 'pendaftar.id', '=', 'pendaftar_asal_sekolah.pendaftar_id')
            ->selectRaw('nama_sekolah, COUNT(*) as jumlah')
            ->groupBy('nama_sekolah')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();
        
        // Pendaftar terbaru
        $pendaftarTerbaru = Pendaftar::with(['jurusan', 'gelombang'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        

        
        return view('admin.dashboard', compact(
            'totalPendaftar', 'pendaftarBaru', 'sudahVerifikasi', 'sudahBayar',
            'menungguVerifikasi', 'ditolak', 'trenHarian', 'statistikJurusan',
            'statistikGelombang', 'sebaranKoordinat', 'topSekolah', 'gelombangAktif',
            'pendaftarTerbaru'
        ));
    }

    public function masterData()
    {
        $jurusan = Jurusan::all();
        $gelombang = Gelombang::all();
        $wilayah = Wilayah::all();
        $provinces = \App\Models\Province::all();
        $regencies = \App\Models\Regency::all();
        $districts = \App\Models\District::all();
        $villages = \App\Models\Village::paginate(50);
        $persyaratan = \App\Models\Persyaratan::orderBy('urutan')->get();
        
        return view('admin.master-data', compact('jurusan', 'gelombang', 'wilayah', 'provinces', 'regencies', 'districts', 'villages', 'persyaratan'));
    }

    // CRUD Persyaratan
    public function storePersyaratan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'jenis' => 'required|in:dokumen,foto,sertifikat',
            'urutan' => 'required|integer|min:1'
        ]);
        
        \App\Models\Persyaratan::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'jenis' => $request->jenis,
            'wajib' => $request->has('wajib'),
            'urutan' => $request->urutan
        ]);

        return redirect()->back()->with('success', 'Persyaratan berhasil ditambahkan');
    }

    public function updatePersyaratan(Request $request, $id)
    {
        $persyaratan = \App\Models\Persyaratan::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'jenis' => 'required|in:dokumen,foto,sertifikat',
            'urutan' => 'required|integer|min:1'
        ]);

        $persyaratan->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'jenis' => $request->jenis,
            'wajib' => $request->has('wajib'),
            'urutan' => $request->urutan
        ]);

        return redirect()->back()->with('success', 'Persyaratan berhasil diupdate');
    }

    public function deletePersyaratan($id)
    {
        $persyaratan = \App\Models\Persyaratan::findOrFail($id);
        $persyaratan->delete();
        
        return redirect()->back()->with('success', 'Persyaratan berhasil dihapus');
    }

    // CRUD Jurusan
    public function storeJurusan(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:jurusan,kode',
            'nama' => 'required',
            'deskripsi' => 'nullable|string|max:500',
            'kuota' => 'required|integer|min:1'
        ]);
        
        $jurusan = Jurusan::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'kuota' => $request->kuota
        ]);

        \App\Models\AuditLog::log('CREATE', 'jurusan', $jurusan->id, null, $jurusan->toArray());
        return redirect()->back()->with('success', 'Jurusan berhasil ditambahkan');
    }

    public function updateJurusan(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);
        
        $request->validate([
            'kode' => 'required|unique:jurusan,kode,' . $id,
            'nama' => 'required',
            'deskripsi' => 'nullable|string|max:500',
            'kuota' => 'required|integer|min:1'
        ]);

        $oldData = $jurusan->toArray();
        $jurusan->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'kuota' => $request->kuota
        ]);

        \App\Models\AuditLog::log('UPDATE', 'jurusan', $jurusan->id, $oldData, $jurusan->fresh()->toArray());
        return redirect()->back()->with('success', 'Jurusan berhasil diupdate');
    }

    public function deleteJurusan($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        
        // Check if jurusan has pendaftar
        if ($jurusan->pendaftar()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus jurusan yang sudah memiliki pendaftar');
        }
        
        $deletedData = $jurusan->toArray();
        $jurusan->delete();
        \App\Models\AuditLog::log('DELETE', 'jurusan', $id, $deletedData, null);
        return redirect()->back()->with('success', 'Jurusan berhasil dihapus');
    }

    // CRUD Gelombang
    public function storeGelombang(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'tahun' => 'required|integer',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'biaya_daftar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        Gelombang::create([
            'nama' => $request->nama,
            'tahun' => $request->tahun,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'biaya_daftar' => $request->biaya_daftar,
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Gelombang berhasil ditambahkan');
    }

    public function updateGelombang(Request $request, $id)
    {
        $gelombang = Gelombang::findOrFail($id);
        
        $request->validate([
            'nama' => 'required',
            'tahun' => 'required|integer',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'biaya_daftar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $gelombang->update([
            'nama' => $request->nama,
            'tahun' => $request->tahun,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'biaya_daftar' => $request->biaya_daftar,
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Gelombang berhasil diupdate');
    }

    public function deleteGelombang($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        
        if ($gelombang->pendaftar()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus gelombang yang sudah memiliki pendaftar');
        }
        
        $gelombang->delete();
        return redirect()->back()->with('success', 'Gelombang berhasil dihapus');
    }

    public function toggleGelombangStatus($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        $newStatus = $gelombang->status === 'aktif' ? 'nonaktif' : 'aktif';
        
        $gelombang->update(['status' => $newStatus]);
        
        $message = $newStatus === 'aktif' ? 'Gelombang diaktifkan' : 'Gelombang dinonaktifkan';
        return redirect()->back()->with('success', $message);
    }

    // CRUD Wilayah
    public function storeWilayah(Request $request)
    {
        try {
            if ($request->type == 'province') {
                $request->validate([
                    'province_id' => 'required|unique:provinces,id',
                    'province_name' => 'required'
                ]);
                
                \App\Models\Province::create([
                    'id' => $request->province_id,
                    'name' => $request->province_name
                ]);
                
                return redirect()->back()->with('success', 'Provinsi berhasil ditambahkan');
            }
            
            if ($request->type == 'regency') {
                $request->validate([
                    'regency_id' => 'required|unique:regencies,id',
                    'province_id' => 'required|exists:provinces,id',
                    'regency_name' => 'required'
                ]);
                
                \App\Models\Regency::create([
                    'id' => $request->regency_id,
                    'province_id' => $request->province_id,
                    'name' => $request->regency_name
                ]);
                
                return redirect()->back()->with('success', 'Kabupaten/Kota berhasil ditambahkan');
            }
            
            // Default wilayah lama
            $request->validate([
                'kodepos' => 'required|unique:wilayah,kodepos',
                'kelurahan' => 'required',
                'kecamatan' => 'required',
                'kabupaten' => 'required',
                'provinsi' => 'required'
            ]);

            Wilayah::create($request->all());
            return redirect()->back()->with('success', 'Wilayah berhasil ditambahkan');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan wilayah: ' . $e->getMessage());
        }
    }

    public function updateWilayah(Request $request, $id)
    {
        try {
            $type = $request->type;
            
            if ($type == 'province') {
                $province = \App\Models\Province::findOrFail($id);
                $request->validate(['province_name' => 'required']);
                $province->update(['name' => $request->province_name]);
                return redirect()->back()->with('success', 'Provinsi berhasil diupdate');
            }
            
            if ($type == 'regency') {
                $regency = \App\Models\Regency::findOrFail($id);
                $request->validate(['regency_name' => 'required']);
                $regency->update(['name' => $request->regency_name]);
                return redirect()->back()->with('success', 'Kabupaten/Kota berhasil diupdate');
            }
            
            if ($type == 'district') {
                $district = \App\Models\District::findOrFail($id);
                $request->validate(['district_name' => 'required']);
                $district->update(['name' => $request->district_name]);
                return redirect()->back()->with('success', 'Kecamatan berhasil diupdate');
            }
            
            if ($type == 'village') {
                $village = \App\Models\Village::findOrFail($id);
                $request->validate(['village_name' => 'required']);
                $village->update(['name' => $request->village_name]);
                return redirect()->back()->with('success', 'Kelurahan berhasil diupdate');
            }
            
            // Default wilayah lama
            $wilayah = Wilayah::findOrFail($id);
            $request->validate([
                'kodepos' => 'required|unique:wilayah,kodepos,' . $id,
                'kelurahan' => 'required',
                'kecamatan' => 'required',
                'kabupaten' => 'required',
                'provinsi' => 'required'
            ]);
            $wilayah->update($request->all());
            return redirect()->back()->with('success', 'Wilayah berhasil diupdate');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupdate: ' . $e->getMessage());
        }
    }

    public function deleteWilayah($id)
    {
        try {
            $type = request('type');
            
            if ($type == 'province') {
                $province = \App\Models\Province::findOrFail($id);
                if ($province->regencies()->count() > 0) {
                    return redirect()->back()->with('error', 'Tidak dapat menghapus provinsi yang memiliki kabupaten/kota');
                }
                $province->delete();
                return redirect()->back()->with('success', 'Provinsi berhasil dihapus');
            }
            
            if ($type == 'regency') {
                $regency = \App\Models\Regency::findOrFail($id);
                if ($regency->districts()->count() > 0) {
                    return redirect()->back()->with('error', 'Tidak dapat menghapus kabupaten/kota yang memiliki kecamatan');
                }
                $regency->delete();
                return redirect()->back()->with('success', 'Kabupaten/Kota berhasil dihapus');
            }
            
            if ($type == 'district') {
                $district = \App\Models\District::findOrFail($id);
                if ($district->villages()->count() > 0) {
                    return redirect()->back()->with('error', 'Tidak dapat menghapus kecamatan yang memiliki kelurahan');
                }
                $district->delete();
                return redirect()->back()->with('success', 'Kecamatan berhasil dihapus');
            }
            
            if ($type == 'village') {
                $village = \App\Models\Village::findOrFail($id);
                $village->delete();
                return redirect()->back()->with('success', 'Kelurahan berhasil dihapus');
            }
            
            // Default wilayah lama
            $wilayah = Wilayah::findOrFail($id);
            $wilayah->delete();
            return redirect()->back()->with('success', 'Wilayah berhasil dihapus');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function monitoringBerkas(Request $request)
    {
        $query = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah', 'berkas']);
        
        // Filter berdasarkan jurusan
        if ($request->filled('jurusan')) {
            $query->where('jurusan_id', $request->jurusan);
        }
        
        // Filter berdasarkan gelombang
        if ($request->filled('gelombang')) {
            $query->where('gelombang_id', $request->gelombang);
        }
        
        // Filter berdasarkan status (menggunakan kolom status yang ada)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where('no_pendaftaran', 'like', '%' . $request->search . '%');
        }
        
        $pendaftar = $query->paginate(20);
        
        // Statistics berdasarkan kolom status yang ada
        $totalPendaftar = Pendaftar::count();
        $berkasLengkap = Pendaftar::where('status', 'ADM_PASS')->count();
        $pendingReview = Pendaftar::where('status', 'SUBMIT')->count();
        $tidakLengkap = Pendaftar::where('status', 'ADM_REJECT')->count();
        
        $jurusan = Jurusan::all();
        $gelombang = Gelombang::all();
        
        return view('admin.monitoring-berkas', compact(
            'pendaftar', 'totalPendaftar', 'berkasLengkap', 
            'pendingReview', 'tidakLengkap', 'jurusan', 'gelombang'
        ));
    }

    public function petaSebaran(Request $request)
    {
        $query = Pendaftar::with(['jurusan', 'gelombang']);
        
        // Filter berdasarkan jurusan
        if ($request->filled('jurusan')) {
            $query->where('jurusan_id', $request->jurusan);
        }
        
        // Filter berdasarkan gelombang
        if ($request->filled('gelombang')) {
            $query->where('gelombang_id', $request->gelombang);
        }
        
        $pendaftar = $query->get();
        
        // Dummy data untuk sebaran kecamatan
        $sebaranKecamatan = collect([
            (object)['kecamatan' => 'Jagakarsa', 'total' => 45],
            (object)['kecamatan' => 'Pasar Minggu', 'total' => 38],
            (object)['kecamatan' => 'Kebayoran Lama', 'total' => 32],
            (object)['kecamatan' => 'Cilandak', 'total' => 28],
            (object)['kecamatan' => 'Pesanggrahan', 'total' => 25]
        ]);
        
        // Dummy data untuk sebaran detail
        $sebaranDetail = collect([
            (object)['kecamatan' => 'Jagakarsa', 'kelurahan' => 'Cipedak', 'jurusan' => 'TKJ', 'total' => 15],
            (object)['kecamatan' => 'Jagakarsa', 'kelurahan' => 'Lenteng Agung', 'jurusan' => 'RPL', 'total' => 12],
            (object)['kecamatan' => 'Pasar Minggu', 'kelurahan' => 'Kebagusan', 'jurusan' => 'MM', 'total' => 10]
        ]);
        
        $jurusan = Jurusan::all();
        $gelombang = Gelombang::all();
        
        return view('admin.peta-sebaran', compact(
            'pendaftar', 'sebaranKecamatan', 'sebaranDetail', 'jurusan', 'gelombang'
        ));
    }

    public function verifikasiBerkas(Request $request, $id)
    {
        $pendaftar = Pendaftar::findOrFail($id);
        $notificationService = new NotificationService();
        
        $pendaftar->update([
            'status' => $request->status,
            'user_verifikasi_adm' => auth()->user()->name ?? 'Admin',
            'tgl_verifikasi_adm' => now()
        ]);
        
        // Send notification based on status
        if ($request->status === 'ADM_PASS') {
            $notificationService->sendBerkasDiterima($pendaftar);
            $biayaDaftar = $pendaftar->gelombang->biaya_daftar ?? 0;
            $notificationService->sendInstruksiBayar($pendaftar, $biayaDaftar);
        } elseif ($request->status === 'ADM_REJECT') {
            $notificationService->sendBerkasDitolak($pendaftar, $request->catatan);
        }
        
        return redirect()->back()->with('success', 'Status verifikasi berhasil diupdate dan notifikasi terkirim');
    }

    public function exportExcel(Request $request)
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Export Excel functionality']);
    }

    public function exportPdf(Request $request)
    {
        // Implementation for PDF export
        return response()->json(['message' => 'Export PDF functionality']);
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function jurusanPublic()
    {
        $jurusan = Jurusan::all();
        return view('jurusan', compact('jurusan'));
    }

    public function pengumuman()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang'])
            ->where('status', 'PAID')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pengumuman', compact('pendaftar'));
    }

    public function setPengumuman(Request $request, $id)
    {
        $request->validate([
            'status_akhir' => 'required|in:LULUS,TIDAK_LULUS,CADANGAN'
        ]);

        $pendaftar = Pendaftar::findOrFail($id);
        $notificationService = new NotificationService();
        
        // Hanya bisa mengumumkan jika status PAID
        if (!$pendaftar->canBeAnnounced()) {
            return back()->with('error', 'Hanya pendaftar dengan status PAID yang bisa diumumkan. Status saat ini: ' . $pendaftar->getStatusLabel());
        }

        $pendaftar->update([
            'status_akhir' => $request->status_akhir,
            'tgl_pengumuman' => now(),
            'user_pengumuman' => auth()->user()->name ?? 'System'
        ]);

        // Send notification
        $notificationService->sendPengumuman($pendaftar, $request->status_akhir);

        $statusLabel = [
            'LULUS' => 'LULUS - Selamat!',
            'TIDAK_LULUS' => 'TIDAK LULUS',
            'CADANGAN' => 'CADANGAN - Menunggu'
        ];

        return back()->with('success', 'Pengumuman berhasil diset dan notifikasi terkirim: ' . $statusLabel[$request->status_akhir]);
    }

    public function auditLogs()
    {
        try {
            // Get the latest 50 log IDs
            $latestIds = \App\Models\AuditLog::orderBy('created_at', 'desc')
                ->limit(50)
                ->pluck('id');
            
            // Delete all logs except the latest 50
            \App\Models\AuditLog::whereNotIn('id', $latestIds)->delete();
            
            // Get the remaining logs
            $logs = \App\Models\AuditLog::orderBy('created_at', 'desc')
                ->paginate(50);
                
        } catch (\Exception $e) {
            // If there's an error, create empty paginator
            $logs = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 50, 1, ['path' => request()->url()]
            );
        }
            
        return view('admin.audit-logs', compact('logs'));
    }
}