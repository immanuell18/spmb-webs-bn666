<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGelombangRequest;
use App\Models\Gelombang;

/**
 * GelombangController
 * Mengelola CRUD gelombang pendaftaran. SRP: hanya urusan gelombang.
 */
class GelombangController extends Controller
{
    public function store(StoreGelombangRequest $request)
    {
        Gelombang::create([
            'nama'        => $request->nama,
            'tahun'       => $request->tahun,
            'tgl_mulai'   => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'biaya_daftar' => $request->biaya_daftar,
            'status'      => $request->status,
        ]);

        return redirect()->back()->with('success', 'Gelombang berhasil ditambahkan');
    }

    public function update(StoreGelombangRequest $request, $id)
    {
        $gelombang = Gelombang::findOrFail($id);

        $gelombang->update([
            'nama'        => $request->nama,
            'tahun'       => $request->tahun,
            'tgl_mulai'   => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'biaya_daftar' => $request->biaya_daftar,
            'status'      => $request->status,
        ]);

        return redirect()->back()->with('success', 'Gelombang berhasil diupdate');
    }

    public function destroy($id)
    {
        $gelombang = Gelombang::findOrFail($id);

        if ($gelombang->pendaftar()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus gelombang yang sudah memiliki pendaftar');
        }

        $gelombang->delete();

        return redirect()->back()->with('success', 'Gelombang berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        $newStatus = $gelombang->status === 'aktif' ? 'nonaktif' : 'aktif';

        $gelombang->update(['status' => $newStatus]);

        $message = $newStatus === 'aktif' ? 'Gelombang diaktifkan' : 'Gelombang dinonaktifkan';

        return redirect()->back()->with('success', $message);
    }
}
