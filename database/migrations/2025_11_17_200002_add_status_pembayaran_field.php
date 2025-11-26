<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftar', 'status_pembayaran')) {
                $table->enum('status_pembayaran', ['belum_bayar', 'menunggu_validasi', 'terbayar', 'reject'])->default('belum_bayar')->after('status');
            }
            if (!Schema::hasColumn('pendaftar', 'status_berkas')) {
                $table->enum('status_berkas', ['lengkap', 'tidak_lengkap'])->default('tidak_lengkap')->after('status_pembayaran');
            }
            if (!Schema::hasColumn('pendaftar', 'status_verifikasi')) {
                $table->enum('status_verifikasi', ['menunggu', 'lulus', 'tolak', 'perbaikan'])->default('menunggu')->after('status_berkas');
            }
            if (!Schema::hasColumn('pendaftar', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
            }
            if (!Schema::hasColumn('pendaftar', 'bukti_bayar')) {
                $table->string('bukti_bayar')->nullable()->after('catatan_verifikasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn(['status_pembayaran', 'status_berkas', 'status_verifikasi', 'catatan_verifikasi', 'bukti_bayar']);
        });
    }
};