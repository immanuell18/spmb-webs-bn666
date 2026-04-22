<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftar;
use App\Services\NotificationService;
use Illuminate\Http\Request;

/**
 * PengumumanController
 * Mengelola pengumuman hasil seleksi akhir (LULUS/TIDAK_LULUS/CADANGAN).
 * SRP: hanya urusan pengumuman kelulusan.
 */
class PengumumanController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Tampilkan daftar pendaftar yang siap diumumkan (status PAID).
     */
    public function index()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang'])
            ->where('status', Pendaftar::STATUS_PAID)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.pengumuman', compact('pendaftar'));
    }

    /**
     * Set hasil akhir pendaftar dan kirim notifikasi.
     */
    public function set(Request $request, $id)
    {
        $request->validate([
            'status_akhir' => 'required|in:LULUS,TIDAK_LULUS,CADANGAN',
        ]);

        $pendaftar = Pendaftar::findOrFail($id);

        if (!$pendaftar->canBeAnnounced()) {
            return back()->with(
                'error',
                "Hanya pendaftar PAID yang bisa diumumkan. Status saat ini: {$pendaftar->getStatusLabel()}"
            );
        }

        $pendaftar->update([
            'status_akhir'    => $request->status_akhir,
            'tgl_pengumuman'  => now(),
            'user_pengumuman' => auth()->user()->name ?? 'System',
        ]);

        $this->notificationService->sendPengumuman($pendaftar, $request->status_akhir);

        $label = match($request->status_akhir) {
            'LULUS'       => 'LULUS — Selamat!',
            'TIDAK_LULUS' => 'TIDAK LULUS',
            'CADANGAN'    => 'CADANGAN — Menunggu',
        };

        return back()->with('success', "Pengumuman berhasil: {$label}");
    }
}
