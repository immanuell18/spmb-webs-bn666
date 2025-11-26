<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->string('nama')->nullable();
            $table->string('email')->nullable();
            $table->enum('status_berkas', ['lengkap', 'tidak_lengkap'])->default('tidak_lengkap');
            $table->enum('status_verifikasi', ['menunggu', 'lulus', 'tolak', 'perbaikan'])->default('menunggu');
            $table->enum('status_pembayaran', ['belum_bayar', 'menunggu_validasi', 'terbayar', 'reject'])->default('belum_bayar');
            $table->decimal('biaya_pendaftaran', 10, 2)->default(250000);
            $table->text('catatan_verifikasi')->nullable();
            $table->unsignedBigInteger('wilayah_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn([
                'nama', 'email', 'status_berkas', 'status_verifikasi', 
                'status_pembayaran', 'biaya_pendaftaran', 'catatan_verifikasi', 'wilayah_id'
            ]);
        });
    }
};