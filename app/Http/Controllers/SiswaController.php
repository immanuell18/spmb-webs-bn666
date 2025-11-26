<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PendaftaranRequest;
use App\Http\Requests\BerkasUploadRequest;
use App\Services\CoordinateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Pendaftar;
use App\Models\PendaftarDataSiswa;
use App\Models\PendaftarDataOrtu;
use App\Models\PendaftarAsalSekolah;
use App\Models\PendaftarBerkas;
use App\Models\Jurusan;
use App\Models\Gelombang;
use App\Models\PaymentTransaction;
use App\Services\PaymentMethodService;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $pendaftar = Pendaftar::with(['dataSiswa', 'dataOrtu', 'asalSekolah', 'berkas'])
                             ->where('user_id', $user->id)
                             ->first();
        $gelombangAktif = Gelombang::getActive();
        
        return view('siswa.dashboard', compact('pendaftar', 'gelombangAktif'));
    }

    public function pendaftaran()
    {
        // Cek apakah ada gelombang yang aktif
        $gelombangAktif = Gelombang::getAvailable();
        
        if ($gelombangAktif->isEmpty()) {
            return redirect()->route('siswa.dashboard')
                           ->with('error', '⚠️ Pendaftaran sedang ditutup. Belum ada gelombang pendaftaran yang dibuka.');
        }
        
        $jurusan = Jurusan::withCount(['pendaftar' => function($query) {
            $query->whereIn('status', ['SUBMIT', 'ADM_PASS', 'PAID']);
        }])->get();
        $pendaftar = Pendaftar::with(['dataSiswa', 'dataOrtu', 'asalSekolah'])
                            ->where('user_id', Auth::id())
                            ->first();
        
        return view('siswa.pendaftaran', compact('jurusan', 'gelombangAktif', 'pendaftar'));
    }

    public function storePendaftaran(PendaftaranRequest $request)
    {
        \Log::info('Form submission received', $request->all());
        
        // Cek gelombang aktif
        $gelombang = Gelombang::find($request->gelombang_id);
        if (!$gelombang || !$gelombang->isActive()) {
            return back()->with('error', '⚠️ Gelombang pendaftaran tidak aktif atau sudah ditutup!');
        }
        
        // Get existing pendaftar first
        $existingPendaftar = Pendaftar::where('user_id', Auth::id())->first();
        
        // Cek kuota jurusan dengan validasi ketat
        $jurusan = Jurusan::withCount(['pendaftar' => function($query) {
            $query->whereIn('status', ['SUBMIT', 'ADM_PASS', 'PAID']);
        }])->find($request->jurusan_id);
        
        if (!$jurusan) {
            return back()->with('error', '⚠️ Jurusan tidak ditemukan!');
        }
        
        if ($jurusan->pendaftar_count >= $jurusan->kuota) {
            return back()->with('error', '⚠️ Kuota jurusan ' . $jurusan->nama . ' sudah penuh! (' . $jurusan->pendaftar_count . '/' . $jurusan->kuota . ') Silakan pilih jurusan lain.');
        }
        
        $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'gelombang_id' => 'required|exists:gelombang,id',
            'nama_lengkap' => 'required|string|max:120',
            'nik' => 'required|string|size:16|unique:pendaftar_data_siswa,nik,' . ($existingPendaftar->dataSiswa->id ?? 'NULL') . ',id',
            'nisn' => 'nullable|string|max:10',
            'tempat_lahir' => 'required|string|max:60',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|max:20',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'nama_ayah' => 'required|string|max:120',
            'nama_ibu' => 'required|string|max:120',
            'pekerjaan_ayah' => 'required|string|max:100',
            'pekerjaan_ibu' => 'required|string|max:100',
            'penghasilan_ortu' => 'required|string|max:50',
            'no_hp_ortu' => 'required|string|max:20',
            'nama_sekolah' => 'required|string|max:150',
            'npsn' => 'nullable|string|max:8',
            'alamat_sekolah' => 'required|string|max:100',
            'nilai_rata' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = Auth::user();
        
        // Cek apakah sudah pernah daftar
        $existingPendaftar = Pendaftar::where('user_id', $user->id)->first();
        
        if ($existingPendaftar) {
            // Cek apakah data detail sudah lengkap
            $hasDataSiswa = PendaftarDataSiswa::where('pendaftar_id', $existingPendaftar->id)->exists();
            $hasDataOrtu = PendaftarDataOrtu::where('pendaftar_id', $existingPendaftar->id)->exists();
            $hasDataSekolah = PendaftarAsalSekolah::where('pendaftar_id', $existingPendaftar->id)->exists();
            
            if ($hasDataSiswa && $hasDataOrtu && $hasDataSekolah) {
                \Log::info('🔄 User sudah lengkap, redirect ke berkas');
                return redirect()->route('siswa.berkas')
                               ->with('info', '✅ Formulir sudah pernah diisi! Silakan lanjutkan upload berkas.');
            } else {
                \Log::info('🔄 Data pendaftar ada tapi detail belum lengkap, lanjut insert detail');
                $pendaftar = $existingPendaftar;
            }
        } else {
            // Generate nomor pendaftaran
            $noPendaftaran = 'SPMB' . date('Y') . str_pad(Pendaftar::count() + 1, 4, '0', STR_PAD_LEFT);

            // Buat data pendaftar dengan status SUBMIT sesuai UKK
            $gelombang = Gelombang::find($request->gelombang_id);
            $pendaftar = Pendaftar::create([
                'user_id' => $user->id,
                'no_pendaftaran' => $noPendaftaran,
                'nama' => $request->nama_lengkap,
                'email' => $user->email,
                'jurusan_id' => $request->jurusan_id,
                'gelombang_id' => $request->gelombang_id,
                'biaya_pendaftaran' => $gelombang->biaya_daftar,
                'tanggal_daftar' => now(),
                'status' => Pendaftar::STATUS_SUBMIT
            ]);
        }



        // Simpan data siswa sesuai UKK requirement
        \Log::info('🔄 Mencoba simpan data siswa untuk pendaftar_id: ' . $pendaftar->id);
        try {
            $dataSiswa = PendaftarDataSiswa::updateOrCreate(
                ['pendaftar_id' => $pendaftar->id],
                [
                    'nik' => $request->nik,
                    'nisn' => $request->nisn ?? null,
                    'nama' => $request->nama_lengkap,
                    'jk' => $request->jenis_kelamin,
                    'agama' => $request->agama,
                    'tmp_lahir' => $request->tempat_lahir,
                    'tgl_lahir' => $request->tanggal_lahir,
                    'alamat' => $request->alamat,
                    'wilayah_id' => null,
                    'lat' => $request->latitude,
                    'lng' => $request->longitude,
                ]
            );
            \Log::info('✅ Data siswa berhasil disimpan dengan ID: ' . $dataSiswa->pendaftar_id);
        } catch (\Exception $e) {
            \Log::error('❌ Error simpan data siswa: ' . $e->getMessage());
            \Log::error('❌ Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error simpan data siswa: ' . $e->getMessage());
        }

        // Simpan data orang tua sesuai UKK requirement
        \Log::info('🔄 Mencoba simpan data ortu untuk pendaftar_id: ' . $pendaftar->id);
        try {
            $dataOrtu = PendaftarDataOrtu::updateOrCreate(
                ['pendaftar_id' => $pendaftar->id],
                [
                    'nama_ayah' => $request->nama_ayah,
                    'pekerjaan_ayah' => $request->pekerjaan_ayah,
                    'hp_ayah' => $request->no_hp_ortu,
                    'nama_ibu' => $request->nama_ibu,
                    'pekerjaan_ibu' => $request->pekerjaan_ibu,
                    'hp_ibu' => $request->no_hp_ortu,
                    'wali_nama' => null,
                    'wali_hp' => null,
                ]
            );
            \Log::info('✅ Data ortu berhasil disimpan dengan ID: ' . $dataOrtu->pendaftar_id);
        } catch (\Exception $e) {
            \Log::error('❌ Error simpan data ortu: ' . $e->getMessage());
            \Log::error('❌ Stack trace: ' . $e->getTraceAsString());
        }

        // Simpan data asal sekolah sesuai UKK requirement
        \Log::info('🔄 Mencoba simpan data sekolah untuk pendaftar_id: ' . $pendaftar->id);
        try {
            $dataSekolah = PendaftarAsalSekolah::updateOrCreate(
                ['pendaftar_id' => $pendaftar->id],
                [
                    'npsn' => $request->npsn,
                    'nama_sekolah' => $request->nama_sekolah,
                    'kabupaten' => $request->alamat_sekolah,
                    'nilai_rata' => $request->nilai_rata ?? 0,
                ]
            );
            \Log::info('✅ Data sekolah berhasil disimpan dengan ID: ' . $dataSekolah->pendaftar_id);
        } catch (\Exception $e) {
            \Log::error('❌ Error simpan data sekolah: ' . $e->getMessage());
            \Log::error('❌ Stack trace: ' . $e->getTraceAsString());
        }

        // Trigger notification event
        event(new \App\Events\UserRegistered($user));
        
        // Clear draft after successful submission
        \Log::info('Registration successful for user: ' . $user->id);
        
        return redirect()->route('siswa.berkas')
                        ->with('success', '🎉 Pendaftaran berhasil! No. Pendaftaran: ' . $pendaftar->no_pendaftaran)
                        ->with('info', '📋 Langkah selanjutnya: Upload berkas yang diperlukan (Ijazah, Rapor, KK, Akta Kelahiran)');
    }

    public function berkas()
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        
        if (!$pendaftar) {
            return redirect()->route('siswa.pendaftaran')->with('error', 'Silakan lengkapi formulir pendaftaran terlebih dahulu');
        }

        $berkas = PendaftarBerkas::where('pendaftar_id', $pendaftar->id)->get();
        
        return view('siswa.berkas', compact('pendaftar', 'berkas'));
    }

    public function uploadBerkas(Request $request)
    {
        \Log::info('Upload berkas request received', $request->all());
        
        try {
            $request->validate([
                'jenis_berkas' => 'required|in:ijazah,rapor,kip,kks,akta,kk,foto',
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);
        } catch (\Exception $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            return back()->with('error', 'Validasi gagal: ' . $e->getMessage());
        }

        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        
        if (!$pendaftar) {
            \Log::error('Pendaftar not found for user: ' . Auth::id());
            return back()->with('error', 'Data pendaftar tidak ditemukan');
        }
        
        \Log::info('Pendaftar found: ' . $pendaftar->id);

        // Upload file
        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $pendaftar->no_pendaftaran . '_' . $request->jenis_berkas . '_' . $originalName . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('berkas', $fileName, 'public');
        
        // Map jenis berkas ke format database
        $jenisMap = [
            'ijazah' => 'IJAZAH',
            'rapor' => 'RAPOR', 
            'kip' => 'KIP',
            'kks' => 'KKS',
            'akta' => 'AKTA',
            'kk' => 'KK',
            'foto' => 'LAINNYA'  // LAINNYA digunakan untuk foto
        ];

        // Hapus berkas lama jika ada
        PendaftarBerkas::where('pendaftar_id', $pendaftar->id)
                      ->where('jenis', $jenisMap[$request->jenis_berkas])
                      ->delete();

        // Simpan data berkas
        try {
            $berkas = PendaftarBerkas::create([
                'pendaftar_id' => $pendaftar->id,
                'jenis' => $jenisMap[$request->jenis_berkas],
                'nama_file' => $fileName,
                'url' => $filePath,
                'ukuran_kb' => round($file->getSize() / 1024),
                'valid' => 0,
                'catatan' => null
            ]);
            \Log::info('Berkas saved successfully: ' . $berkas->id);
        } catch (\Exception $e) {
            \Log::error('Failed to save berkas: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan berkas: ' . $e->getMessage());
        }

        // Update catatan admin jika berkas sudah lengkap
        $totalBerkas = PendaftarBerkas::where('pendaftar_id', $pendaftar->id)->count();
        if ($totalBerkas >= 4) { // Minimal 4 berkas wajib
            $pendaftar->update([
                'catatan_admin' => 'Berkas lengkap, menunggu verifikasi administrasi',
                'status' => 'SUBMIT' // Tetap SUBMIT sampai admin verifikasi
            ]);
        }

        $nextStep = '';
        if ($totalBerkas >= 4) {
            $nextStep = 'Berkas lengkap! Menunggu verifikasi administrasi.';
        } else {
            $sisaBerkas = 4 - $totalBerkas;
            $nextStep = "Upload {$sisaBerkas} berkas lagi untuk melengkapi persyaratan.";
        }
        
        return back()->with('success', 'Berkas berhasil diupload! 📁')->with('info', $nextStep);
    }
    
    public function deleteBerkas($id)
    {
        $berkas = PendaftarBerkas::findOrFail($id);
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        
        if (!$pendaftar || $berkas->pendaftar_id !== $pendaftar->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        // Hapus file dari storage
        if (Storage::disk('public')->exists($berkas->url)) {
            Storage::disk('public')->delete($berkas->url);
        }
        
        // Hapus record dari database
        $berkas->delete();
        
        // Update catatan admin jika berkas tidak lengkap
        $totalBerkas = PendaftarBerkas::where('pendaftar_id', $pendaftar->id)->count();
        if ($totalBerkas < 4) {
            $pendaftar->update(['catatan_admin' => 'Berkas belum lengkap, upload minimal 4 berkas']);
        }
        
        return response()->json(['success' => true, 'message' => 'Berkas berhasil dihapus']);
    }

    public function status()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah', 'berkas'])
                             ->where('user_id', Auth::id())
                             ->first();
        
        return view('siswa.status', compact('pendaftar'));
    }
    
    public function statusAjax()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah', 'berkas'])
                             ->where('user_id', Auth::id())
                             ->first();
        
        if (!$pendaftar) {
            return response()->json([
                'status' => 'not_registered',
                'message' => 'Belum terdaftar'
            ]);
        }
        
        $hasDataSiswa = $pendaftar->dataSiswa !== null;
        $hasDataOrtu = $pendaftar->dataOrtu !== null;
        $hasDataSekolah = $pendaftar->asalSekolah !== null;
        $isFormComplete = $hasDataSiswa && $hasDataOrtu && $hasDataSekolah;
        
        $berkasCount = $pendaftar->berkas->count();
        $isBerkasComplete = $berkasCount >= 4;
        
        $isVerified = $pendaftar->status === 'ADM_PASS';
        
        $progress = 0;
        if($isFormComplete) $progress += 33;
        if($isBerkasComplete) $progress += 33;
        if($isVerified) $progress += 34;
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'form_complete' => $isFormComplete,
                'berkas_complete' => $isBerkasComplete,
                'verified' => $isVerified,
                'berkas_count' => $berkasCount,
                'progress' => $progress,
                'no_pendaftaran' => $pendaftar->no_pendaftaran,
                'jurusan' => $pendaftar->jurusan->nama ?? '-',
                'gelombang' => $pendaftar->gelombang->nama ?? '-',
                'status_pendaftar' => $pendaftar->status
            ]
        ]);
    }

    public function pembayaran()
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        
        if (!$pendaftar || !$pendaftar->canProceedToPayment()) {
            return redirect()->route('siswa.status')->with('error', 'Berkas belum diverifikasi atau ditolak. Status: ' . ($pendaftar->getStatusLabel() ?? 'Belum mendaftar'));
        }

        return view('siswa.pembayaran', compact('pendaftar'));
    }

    public function bayar()
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        return view('siswa.bayar', compact('pendaftar'));
    }

    public function uploadBuktiBayar(Request $request)
    {
        $request->validate([
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        
        if (!$pendaftar) {
            return back()->with('error', 'Data pendaftar tidak ditemukan');
        }
        
        if (!$pendaftar->canProceedToPayment()) {
            return back()->with('error', 'Berkas belum diverifikasi. Status: ' . $pendaftar->getStatusLabel());
        }
        
        $file = $request->file('bukti_bayar');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $pendaftar->no_pendaftaran . '_bukti_bayar_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('pembayaran', $fileName, 'public');

        // Hapus bukti bayar lama jika ada
        PendaftarBerkas::where('pendaftar_id', $pendaftar->id)
                      ->where('jenis', 'BUKTI_BAYAR')
                      ->delete();

        // Simpan bukti bayar ke tabel berkas
        PendaftarBerkas::create([
            'pendaftar_id' => $pendaftar->id,
            'jenis' => 'BUKTI_BAYAR',
            'nama_file' => $fileName,
            'url' => $filePath,
            'ukuran_kb' => round($file->getSize() / 1024),
            'valid' => 0,
            'catatan' => 'Menunggu validasi pembayaran'
        ]);
        
        $pendaftar->update([
            'catatan_admin' => 'Bukti pembayaran telah diupload, menunggu validasi keuangan'
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload! Tim keuangan akan memverifikasi dalam 1x24 jam.');
    }

    public function profile()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah'])
                             ->where('user_id', Auth::id())
                             ->first();
        
        return view('siswa.profile', compact('pendaftar'));
    }

    public function cetakKartu()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa'])
                             ->where('user_id', Auth::id())
                             ->first();
        
        return view('siswa.cetak-kartu', compact('pendaftar'));
    }

    public function cetakKartuPdf()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah'])
                             ->where('user_id', Auth::id())
                             ->first();
        
        if (!$pendaftar) {
            return redirect()->route('siswa.pendaftaran')->with('error', 'Data pendaftar tidak ditemukan');
        }

        $pdf = Pdf::loadView('siswa.pdf.kartu-pendaftaran', compact('pendaftar'));
        return $pdf->download('Kartu_Pendaftaran_' . $pendaftar->no_pendaftaran . '.pdf');
    }

    public function cetakBuktiPdf()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang'])
                             ->where('user_id', Auth::id())
                             ->first();
        
        if (!$pendaftar || !$pendaftar->bukti_bayar) {
            return redirect()->route('siswa.cetak-kartu')->with('error', 'Bukti pembayaran tidak ditemukan');
        }

        $pdf = Pdf::loadView('siswa.pdf.bukti-pembayaran', compact('pendaftar'));
        return $pdf->download('Bukti_Pembayaran_' . $pendaftar->no_pendaftaran . '.pdf');
    }

    public function cetakPengumumanPdf()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang'])
                             ->where('user_id', Auth::id())
                             ->first();
        
        if (!$pendaftar || !$pendaftar->status_akhir) {
            return redirect()->route('siswa.cetak-kartu')->with('error', 'Pengumuman belum tersedia');
        }

        $pdf = Pdf::loadView('siswa.pdf.surat-pengumuman', compact('pendaftar'));
        return $pdf->download('Surat_Pengumuman_' . $pendaftar->no_pendaftaran . '.pdf');
    }

    public function serveBerkas($filename)
    {
        $user = Auth::user();
        
        // Check if user is authorized to access this file
        if ($user->role === 'pendaftar') {
            $pendaftar = Pendaftar::where('user_id', $user->id)->first();
            if (!$pendaftar || !str_starts_with($filename, $pendaftar->no_pendaftaran)) {
                abort(403, 'Unauthorized access to file');
            }
        } elseif (!in_array($user->role, ['admin', 'verifikator_adm', 'keuangan', 'kepsek'])) {
            abort(403, 'Unauthorized access to file');
        }
        
        $filePath = storage_path('app/public/berkas/' . $filename);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        
        return response()->file($filePath);
    }
}