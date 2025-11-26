<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing users dengan role lama ke role baru
        DB::table('users')->where('role', 'calon_siswa')->update(['role' => 'pendaftar']);
        DB::table('users')->where('role', 'verifikator')->update(['role' => 'verifikator_adm']);
        
        // Pastikan semua user punya kolom hp dan aktif
        DB::table('users')->whereNull('hp')->update(['hp' => '']);
        DB::table('users')->whereNull('aktif')->update(['aktif' => 1]);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'pendaftar')->update(['role' => 'calon_siswa']);
        DB::table('users')->where('role', 'verifikator_adm')->update(['role' => 'verifikator']);
    }
};