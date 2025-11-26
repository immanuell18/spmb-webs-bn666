<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update users table untuk sesuai dengan rancangan UKK
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'hp')) {
                $table->string('hp', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'aktif')) {
                $table->tinyInteger('aktif')->default(1)->after('role');
            }
        });

        // 2. Update pendaftar_data_siswa untuk agama sesuai UKK
        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftar_data_siswa', 'agama')) {
                $table->string('agama', 20)->nullable()->after('jk');
            }
        });

        // 3. Tambah kolom audit sesuai UKK di pendaftar
        Schema::table('pendaftar', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftar', 'user_verifikasi_adm')) {
                $table->string('user_verifikasi_adm', 100)->nullable();
            }
            if (!Schema::hasColumn('pendaftar', 'tgl_verifikasi_adm')) {
                $table->datetime('tgl_verifikasi_adm')->nullable();
            }
            if (!Schema::hasColumn('pendaftar', 'user_verifikasi_payment')) {
                $table->string('user_verifikasi_payment', 100)->nullable();
            }
            if (!Schema::hasColumn('pendaftar', 'tgl_verifikasi_payment')) {
                $table->datetime('tgl_verifikasi_payment')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['hp', 'aktif']);
        });
        
        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            $table->dropColumn('agama');
        });
        
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn(['user_verifikasi_adm', 'tgl_verifikasi_adm', 'user_verifikasi_payment', 'tgl_verifikasi_payment']);
        });
    }
};