<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftar;
use App\Services\NotificationService;
use Illuminate\Http\Request;

/**
 * VerifikasiController
 * Mengelola proses verifikasi berkas administrasi pendaftar.
 * SRP: hanya urusan verifikasi berkas.
 */
class VerifikasiController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Update status verifikasi berkas dan kirim notifikasi.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status'  => 'required|in:ADM_PASS,ADM_REJECT',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pendaftar = Pendaftar::findOrFail($id);

        $pendaftar->update([
            'status'              => $request->status,
            'user_verifikasi_adm' => auth()->user()->name ?? 'Admin',
            'tgl_verifikasi_adm'  => now(),
        ]);

        // Kirim notifikasi sesuai hasil verifikasi
        if ($request->status === Pendaftar::STATUS_ADM_PASS) {
            $this->notificationService->sendBerkasDiterima($pendaftar);
            $biayaDaftar = $pendaftar->gelombang->biaya_daftar ?? 0;
            $this->notificationService->sendInstruksiBayar($pendaftar, $biayaDaftar);
        } elseif ($request->status === Pendaftar::STATUS_ADM_REJECT) {
            $this->notificationService->sendBerkasDitolak($pendaftar, $request->catatan);
        }

        $label = $request->status === Pendaftar::STATUS_ADM_PASS
            ? 'Berkas DITERIMA — notifikasi bayar terkirim'
            : 'Berkas DITOLAK — notifikasi penolakan terkirim';

        return redirect()->back()->with('success', "Verifikasi berhasil: {$label}");
    }
}
