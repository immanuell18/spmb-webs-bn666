<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGelombangRequest;
use App\Models\AuditLog;
use App\Models\Gelombang;
use Illuminate\Http\Request;

/**
 * GelombangController (root namespace)
 *
 * Dipakai oleh route resource `admin/gelombang` (index, create, edit, show).
 * CRUD store/update/delete juga ada di sini karena route resource
 * terdaftar ke controller ini di web.php.
 *
 * Form Requests: StoreGelombangRequest untuk validasi store & update.
 */
class GelombangController extends Controller
{
    public function index()
    {
        $gelombang = Gelombang::orderBy('tahun', 'desc')
                              ->orderBy('tgl_mulai', 'desc')
                              ->paginate(20);

        return view('admin.gelombang.index', compact('gelombang'));
    }

    public function create()
    {
        return view('admin.gelombang.create');
    }

    /**
     * Simpan gelombang baru — validasi via StoreGelombangRequest.
     */
    public function store(StoreGelombangRequest $request)
    {
        $gelombang = Gelombang::create($request->validated());

        AuditLog::log('CREATE', 'gelombang', $gelombang->id, null, $gelombang->toArray());

        return redirect()->route('admin.gelombang.index')
                         ->with('success', 'Gelombang pendaftaran berhasil ditambahkan');
    }

    public function edit(Gelombang $gelombang)
    {
        return view('admin.gelombang.edit', compact('gelombang'));
    }

    /**
     * Update gelombang — validasi via StoreGelombangRequest.
     */
    public function update(StoreGelombangRequest $request, Gelombang $gelombang)
    {
        $oldData = $gelombang->toArray();
        $gelombang->update($request->validated());

        AuditLog::log('UPDATE', 'gelombang', $gelombang->id, $oldData, $gelombang->fresh()->toArray());

        return redirect()->route('admin.gelombang.index')
                         ->with('success', 'Gelombang berhasil diperbarui');
    }

    /**
     * Hapus gelombang — guard: tidak bisa hapus kalau sudah ada pendaftar.
     */
    public function destroy(Gelombang $gelombang)
    {
        if ($gelombang->pendaftar()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus gelombang yang sudah memiliki pendaftar');
        }

        $deletedData = $gelombang->toArray();
        $gelombang->delete();

        AuditLog::log('DELETE', 'gelombang', $gelombang->id, $deletedData, null);

        return redirect()->route('admin.gelombang.index')
                         ->with('success', 'Gelombang pendaftaran berhasil dihapus');
    }

    /**
     * Toggle aktif/nonaktif gelombang.
     */
    public function toggleStatus(Gelombang $gelombang)
    {
        $newStatus = $gelombang->status === 'aktif' ? 'nonaktif' : 'aktif';
        $gelombang->update(['status' => $newStatus]);

        $message = $newStatus === 'aktif' ? 'Gelombang diaktifkan' : 'Gelombang dinonaktifkan';

        return redirect()->back()->with('success', $message);
    }
}
