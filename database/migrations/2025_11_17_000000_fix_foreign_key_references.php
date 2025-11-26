<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix foreign key reference dari 'pengguna' ke 'users'
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Pastikan tabel users memiliki kolom yang diperlukan
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('calon_siswa')->after('password');
            }
            if (!Schema::hasColumn('users', 'hp')) {
                $table->string('hp', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'aktif')) {
                $table->tinyInteger('aktif')->default(1)->after('role');
            }
        });
        
        // Pastikan enum status pendaftar sesuai dengan yang digunakan
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->enum('status', ['SUBMIT', 'ADM_PASS', 'ADM_REJECT', 'PAID', 'LULUS', 'TIDAK_LULUS', 'CADANGAN'])
                  ->default('SUBMIT')
                  ->change();
        });
        
        // Pastikan kolom koordinat ada di pendaftar_data_siswa
        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftar_data_siswa', 'lat')) {
                $table->decimal('lat', 10, 8)->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('pendaftar_data_siswa', 'lng')) {
                $table->decimal('lng', 11, 8)->nullable()->after('lat');
            }
            if (!Schema::hasColumn('pendaftar_data_siswa', 'koordinat_valid')) {
                $table->boolean('koordinat_valid')->default(false)->after('lng');
            }
            if (!Schema::hasColumn('pendaftar_data_siswa', 'sumber_koordinat')) {
                $table->string('sumber_koordinat')->nullable()->after('koordinat_valid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('pengguna');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'hp', 'aktif']);
        });
        
        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng', 'koordinat_valid', 'sumber_koordinat']);
        });
    }
};