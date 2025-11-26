<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RoleRedirectController extends Controller
{
    public function redirectToDashboard()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        switch ($user->role) {
            case User::ROLE_ADMIN:
                return redirect()->route('admin.dashboard');
            case User::ROLE_KEUANGAN:
                return redirect()->route('keuangan.dashboard');
            case User::ROLE_KEPSEK:
                return redirect()->route('kepsek.dashboard');
            case User::ROLE_VERIFIKATOR_ADM:
                return redirect()->route('verifikator.dashboard');
            case User::ROLE_PENDAFTAR:
                return redirect()->route('siswa.dashboard');
            default:
                return redirect()->route('beranda');
        }
    }
}