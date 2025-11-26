<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\Jurusan;
use App\Models\Gelombang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikatorController extends Controller
{
    public function dashboard()
    {
        $gelombangAktif = Gelombang::getActive();
        
        $stats = [
            'total_pendaftar' => Pendaftar::count(),
            'menunggu_verifikasi' => Pendaftar::where('status', 'SUBMIT')->count(),
            'terverifikasi' => Pendaftar::where('status', 'ADM_PASS')->count(),
            'perlu_perbaikan' => Pendaftar::where('status', 'ADM_REJECT')->count(),
            'ditolak' => Pendaftar::where('status', 'ADM_REJECT')->count(),
            'sudah_bayar' => Pendaftar::where('status', 'PAID')->count(),
        ];

        $pendaftar_terbaru = Pendaftar::with(['jurusan', 'gelombang', 'berkas'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('verifikator.dashboard', compact('stats', 'pendaftar_terbaru', 'gelombangAktif'));
    }

    public function administrasi()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'berkas'])
            ->whereIn('status', ['SUBMIT', 'ADM_PASS', 'ADM_REJECT', 'PAID']) // Tampilkan semua termasuk yang sudah bayar
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('verifikator.administrasi', compact('pendaftar'));
    }

    public function verifikasi(Request $request)
    {
        $query = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'berkas'])
            ->whereIn('status', ['SUBMIT', 'ADM_PASS', 'ADM_REJECT', 'PAID']);

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->status;
            
            // Jika filter status akhir (LULUS, TIDAK_LULUS, CADANGAN)
            if (in_array($status, ['LULUS', 'TIDAK_LULUS', 'CADANGAN'])) {
                $query->where('status_akhir', $status);
            } else {
                // Jika filter status proses (SUBMIT, ADM_PASS, dll)
                $query->where('status', $status);
            }
        }

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $pendaftar = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('verifikator.verifikasi', compact('pendaftar'));
    }

    public function detailPendaftar($id)
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah', 'berkas'])
            ->findOrFail($id);

        return view('verifikator.detail', compact('pendaftar'));
    }

    public function prosesVerifikasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:lulus,tolak',
            'catatan' => 'nullable|string|max:500'
        ]);

        $pendaftar = Pendaftar::findOrFail($id);
        
        // Hanya bisa verifikasi jika status SUBMIT
        if ($pendaftar->status !== Pendaftar::STATUS_SUBMIT) {
            return redirect()->back()->with('error', 'Pendaftar sudah diverifikasi atau dalam status lain');
        }
        
        // Update status sesuai UKK - hanya ADM_PASS atau ADM_REJECT
        $newStatus = $request->status === 'lulus' ? Pendaftar::STATUS_ADM_PASS : Pendaftar::STATUS_ADM_REJECT;
        
        $pendaftar->update([
            'status' => $newStatus,
            'catatan_admin' => strip_tags($request->catatan),
            'user_verifikasi_adm' => auth()->user()->name,
            'tgl_verifikasi_adm' => now(),
        ]);

        // Trigger notification events
        if ($newStatus === Pendaftar::STATUS_ADM_PASS) {
            event(new \App\Events\PaymentInstructionSent($pendaftar));
        } else {
            event(new \App\Events\DocumentVerificationRequested($pendaftar, strip_tags($request->catatan ?? 'Berkas memerlukan perbaikan')));
        }

        $message = $newStatus === Pendaftar::STATUS_ADM_PASS ? 'Berkas disetujui' : 'Berkas ditolak';
        return redirect()->back()->with('success', $message);
    }
}