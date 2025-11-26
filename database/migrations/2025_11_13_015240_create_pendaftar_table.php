<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftar', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->datetime('tanggal_daftar');
            $table->string('no_pendaftaran', 20)->unique();
            $table->unsignedBigInteger('gelombang_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->enum('status', ['SUBMIT', 'ADM_PASS', 'ADM_REJECT', 'PAID']);
            $table->string('user_verifikasi_adm', 100)->nullable();
            $table->datetime('tgl_verifikasi_adm')->nullable();
            $table->string('user_verifikasi_payment', 100)->nullable();
            $table->datetime('tgl_verifikasi_payment')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('pengguna');
            $table->foreign('gelombang_id')->references('id')->on('gelombang');
            $table->foreign('jurusan_id')->references('id')->on('jurusan');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftar');
    }
};