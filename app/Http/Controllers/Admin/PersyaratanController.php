<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersyaratanRequest;
use App\Models\Persyaratan;

/**
 * PersyaratanController
 * Mengelola CRUD persyaratan dokumen pendaftaran.
 * SRP: hanya urusan persyaratan.
 */
class PersyaratanController extends Controller
{
    public function store(StorePersyaratanRequest $request)
    {
        Persyaratan::create([
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'jenis'     => $request->jenis,
            'wajib'     => $request->boolean('wajib'),
            'urutan'    => $request->urutan,
        ]);

        return redirect()->back()->with('success', 'Persyaratan berhasil ditambahkan');
    }

    public function update(StorePersyaratanRequest $request, Persyaratan $persyaratan)
    {
        $persyaratan->update([
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'jenis'     => $request->jenis,
            'wajib'     => $request->boolean('wajib'),
            'urutan'    => $request->urutan,
        ]);

        return redirect()->back()->with('success', 'Persyaratan berhasil diupdate');
    }

    public function destroy(Persyaratan $persyaratan)
    {
        $persyaratan->delete();

        return redirect()->back()->with('success', 'Persyaratan berhasil dihapus');
    }
}
