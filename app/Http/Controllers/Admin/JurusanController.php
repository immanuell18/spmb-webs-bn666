<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJurusanRequest;
use App\Models\AuditLog;
use App\Models\Jurusan;

/**
 * JurusanController
 * Mengelola CRUD jurusan. SRP: hanya urusan jurusan.
 */
class JurusanController extends Controller
{
    public function store(StoreJurusanRequest $request)
    {
        $jurusan = Jurusan::create([
            'kode'      => $request->kode,
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'kuota'     => $request->kuota,
        ]);

        AuditLog::log('CREATE', 'jurusan', $jurusan->id, null, $jurusan->toArray());

        return redirect()->back()->with('success', 'Jurusan berhasil ditambahkan');
    }

    public function update(StoreJurusanRequest $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $oldData = $jurusan->toArray();

        $jurusan->update([
            'kode'      => $request->kode,
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'kuota'     => $request->kuota,
        ]);

        AuditLog::log('UPDATE', 'jurusan', $jurusan->id, $oldData, $jurusan->fresh()->toArray());

        return redirect()->back()->with('success', 'Jurusan berhasil diupdate');
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);

        if ($jurusan->pendaftar()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus jurusan yang sudah memiliki pendaftar');
        }

        $deletedData = $jurusan->toArray();
        $jurusan->delete();

        AuditLog::log('DELETE', 'jurusan', $id, $deletedData, null);

        return redirect()->back()->with('success', 'Jurusan berhasil dihapus');
    }
}
