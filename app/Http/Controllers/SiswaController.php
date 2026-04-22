<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranRequest;
use App\Http\Requests\UploadBerkasRequest;
use App\Http\Requests\UploadBuktiBayarRequest;
use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\PendaftarBerkas;
use App\Services\BerkasService;
use App\Services\PendaftaranService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * SiswaController
 *
 * Responsibility: Koordinasi request-response untuk siswa/pendaftar.
 * Logika bisnis sudah dipindah ke:
 *   - PendaftaranService → proses pendaftaran
 *   - BerkasService      → upload/delete berkas
 */
class SiswaController extends Controller
{
    public function __construct(
        private readonly PendaftaranService $pendaftaranService,
        private readonly BerkasService      $berkasService,
    ) {}

    // =============================================
    // DASHBOARD & PROFIL
    // =============================================

    public function dashboard()
    {
        $pendaftar = Pendaftar::with(['dataSiswa', 'dataOrtu', 'asalSekolah', 'berkas'])
            ->where('user_id', Auth::id())
            ->first();

        return view('siswa.dashboard', [
            'pendaftar'      => $pendaftar,
            'gelombangAktif' => Gelombang::getActive(),
        ]);
    }

    public function profile()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah'])
            ->where('user_id', Auth::id())
            ->first();

        return view('siswa.profile', compact('pendaftar'));
    }

    // =============================================
    // PENDAFTARAN
    // =============================================

    public function pendaftaran()
    {
        $gelombangAktif = Gelombang::getAvailable();

        if ($gelombangAktif->isEmpty()) {
            return redirect()->route('siswa.dashboard')
                ->with('error', '⚠️ Pendaftaran sedang ditutup. Belum ada gelombang pendaftaran yang dibuka.');
        }

        $jurusan = Jurusan::withCount(['pendaftar' => fn($q) => $q
            ->whereIn('status', ['SUBMIT', 'ADM_PASS', 'PAID'])
        ])->get();

        $pendaftar = Pendaftar::with(['dataSiswa', 'dataOrtu', 'asalSekolah'])
            ->where('user_id', Auth::id())
            ->first();

        return view('siswa.pendaftaran', compact('jurusan', 'gelombangAktif', 'pendaftar'));
    }

    public function storePendaftaran(PendaftaranRequest $request)
    {
        $user = Auth::user();

        try {
            $gelombang = $this->pendaftaranService->validasiGelombang($request->gelombang_id);
            $this->pendaftaranService->validasiKuotaJurusan($request->jurusan_id);
            $pendaftar = $this->pendaftaranService->prosesPendaftaran($user, $gelombang, $request->validated());
            $this->pendaftaranService->kirimEmailKonfirmasi($user, $pendaftar);

            event(new \App\Events\UserRegistered($user));

            return redirect()->route('siswa.berkas')
                ->with('success', "🎉 Pendaftaran berhasil! No. Pendaftaran: {$pendaftar->no_pendaftaran}")
                ->with('info', '📋 Langkah selanjutnya: Upload berkas (Ijazah, Rapor, KK, Akta Kelahiran).');

        } catch (\RuntimeException $e) {
            return back()->with('error', '⚠️ ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Pendaftaran gagal', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->with('error', '❌ Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    // =============================================
    // BERKAS
    // =============================================

    public function berkas()
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();

        if (!$pendaftar) {
            return redirect()->route('siswa.pendaftaran')
                ->with('error', 'Silakan lengkapi formulir pendaftaran terlebih dahulu');
        }

        $berkas = PendaftarBerkas::where('pendaftar_id', $pendaftar->id)->get();

        return view('siswa.berkas', compact('pendaftar', 'berkas'));
    }

    public function uploadBerkas(UploadBerkasRequest $request)
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();

        if (!$pendaftar) {
            return back()->with('error', 'Data pendaftar tidak ditemukan');
        }

        $this->berkasService->upload($pendaftar, $request->jenis_berkas, $request->file('file'));

        $sisa     = $this->berkasService->sisaBerkas($pendaftar->id);
        $nextStep = $sisa === 0
            ? 'Berkas lengkap! Menunggu verifikasi administrasi.'
            : "Upload {$sisa} berkas lagi untuk melengkapi persyaratan.";

        return back()->with('success', 'Berkas berhasil diupload! 📁')->with('info', $nextStep);
    }

    public function deleteBerkas($id)
    {
        $berkas    = PendaftarBerkas::findOrFail($id);
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();

        if (!$pendaftar || $berkas->pendaftar_id !== $pendaftar->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $this->berkasService->delete($berkas, $pendaftar);

        return response()->json(['success' => true, 'message' => 'Berkas berhasil dihapus']);
    }

    // =============================================
    // STATUS
    // =============================================

    public function status()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah', 'berkas'])
            ->where('user_id', Auth::id())
            ->first();

        return view('siswa.status', compact('pendaftar'));
    }

    public function statusAjax()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'berkas'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$pendaftar) {
            return response()->json(['status' => 'not_registered', 'message' => 'Belum terdaftar']);
        }

        $berkasCount    = $pendaftar->berkas->count();
        $isFormComplete = $pendaftar->dataSiswa && $pendaftar->dataOrtu && $pendaftar->asalSekolah;
        $isBerkasComplete = $berkasCount >= BerkasService::BERKAS_WAJIB_MINIMAL;
        $isVerified     = $pendaftar->status === Pendaftar::STATUS_ADM_PASS;

        $progress = ($isFormComplete ? 33 : 0)
                  + ($isBerkasComplete ? 33 : 0)
                  + ($isVerified ? 34 : 0);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'form_complete'    => $isFormComplete,
                'berkas_complete'  => $isBerkasComplete,
                'verified'         => $isVerified,
                'berkas_count'     => $berkasCount,
                'progress'         => $progress,
                'no_pendaftaran'   => $pendaftar->no_pendaftaran,
                'jurusan'          => $pendaftar->jurusan->nama ?? '-',
                'gelombang'        => $pendaftar->gelombang->nama ?? '-',
                'status_pendaftar' => $pendaftar->status,
            ],
        ]);
    }

    // =============================================
    // PEMBAYARAN
    // =============================================

    public function pembayaran()
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();

        if (!$pendaftar || !$pendaftar->canProceedToPayment()) {
            return redirect()->route('siswa.status')
                ->with('error', 'Berkas belum diverifikasi. Status: ' . ($pendaftar?->getStatusLabel() ?? 'Belum mendaftar'));
        }

        return view('siswa.pembayaran', compact('pendaftar'));
    }

    public function bayar()
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        return view('siswa.bayar', compact('pendaftar'));
    }

    public function uploadBuktiBayar(UploadBuktiBayarRequest $request)
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();

        if (!$pendaftar) {
            return back()->with('error', 'Data pendaftar tidak ditemukan');
        }

        if (!$pendaftar->canProceedToPayment()) {
            return back()->with('error', 'Berkas belum diverifikasi. Status: ' . $pendaftar->getStatusLabel());
        }

        $this->berkasService->uploadBuktiBayar($pendaftar, $request->file('bukti_bayar'));

        return back()->with('success', 'Bukti pembayaran berhasil diupload! Tim keuangan akan memverifikasi dalam 1x24 jam.');
    }

    // =============================================
    // CETAK / PDF
    // =============================================

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
            ->firstOrFail();

        $pdf = Pdf::loadView('siswa.pdf.kartu-pendaftaran', compact('pendaftar'));
        return $pdf->download("Kartu_Pendaftaran_{$pendaftar->no_pendaftaran}.pdf");
    }

    public function cetakBuktiPdf()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$pendaftar || !$pendaftar->bukti_bayar) {
            return redirect()->route('siswa.cetak-kartu')
                ->with('error', 'Bukti pembayaran tidak ditemukan');
        }

        $pdf = Pdf::loadView('siswa.pdf.bukti-pembayaran', compact('pendaftar'));
        return $pdf->download("Bukti_Pembayaran_{$pendaftar->no_pendaftaran}.pdf");
    }

    public function cetakPengumumanPdf()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$pendaftar || !$pendaftar->status_akhir) {
            return redirect()->route('siswa.cetak-kartu')
                ->with('error', 'Pengumuman belum tersedia');
        }

        $pdf = Pdf::loadView('siswa.pdf.surat-pengumuman', compact('pendaftar'));
        return $pdf->download("Surat_Pengumuman_{$pendaftar->no_pendaftaran}.pdf");
    }

    // =============================================
    // FILE SERVING (SECURE)
    // =============================================

    public function serveBerkas(string $filename)
    {
        $user = Auth::user();

        if ($user->role === 'pendaftar') {
            $pendaftar = Pendaftar::where('user_id', $user->id)->first();
            if (!$pendaftar || !str_starts_with($filename, $pendaftar->no_pendaftaran)) {
                abort(403, 'Unauthorized access to file');
            }
        } elseif (!in_array($user->role, ['admin', 'verifikator_adm', 'keuangan', 'kepsek'])) {
            abort(403, 'Unauthorized access to file');
        }

        $filePath = storage_path("app/public/berkas/{$filename}");

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->file($filePath);
    }
}