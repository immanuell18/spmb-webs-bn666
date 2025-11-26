<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->keyBy('key');
        return view('admin.system-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'biaya_pendaftaran' => 'required|numeric|min:0'
        ]);

        SystemSetting::set('biaya_pendaftaran', $request->biaya_pendaftaran, 'Biaya pendaftaran SPMB dalam rupiah');

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui');
    }
}