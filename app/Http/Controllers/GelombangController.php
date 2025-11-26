<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gelombang;

class GelombangController extends Controller
{
    public function index()
    {
        $gelombang = Gelombang::orderBy('tahun', 'desc')->orderBy('tgl_mulai', 'desc')->get();
        return view('admin.gelombang.index', compact('gelombang'));
    }

    public function create()
    {
        return view('admin.gelombang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2024|max:2030',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'biaya_daftar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        Gelombang::create($request->all());

        return redirect()->route('admin.gelombang.index')
                        ->with('success', 'Gelombang pendaftaran berhasil ditambahkan');
    }

    public function edit(Gelombang $gelombang)
    {
        return view('admin.gelombang.edit', compact('gelombang'));
    }

    public function update(Request $request, Gelombang $gelombang)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2024|max:2030',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'biaya_daftar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $gelombang->update($request->all());

        return redirect()->route('admin.master-data')
                        ->with('success', 'Gelombang berhasil diperbarui');
    }

    public function destroy(Gelombang $gelombang)
    {
        // Cek apakah ada pendaftar di gelombang ini
        if ($gelombang->pendaftar()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus gelombang yang sudah memiliki pendaftar');
        }

        $gelombang->delete();
        return redirect()->route('admin.gelombang.index')
                        ->with('success', 'Gelombang pendaftaran berhasil dihapus');
    }

    public function toggleStatus(Gelombang $gelombang)
    {
        $newStatus = $gelombang->status === 'aktif' ? 'nonaktif' : 'aktif';
        $gelombang->update(['status' => $newStatus]);

        $message = $newStatus === 'aktif' ? 'Gelombang diaktifkan' : 'Gelombang dinonaktifkan';
        return redirect()->route('admin.master-data')->with('success', $message);
    }
}