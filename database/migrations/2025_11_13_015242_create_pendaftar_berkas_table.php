<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftar_berkas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pendaftar_id');
            $table->enum('jenis', ['IJAZAH', 'RAPOR', 'KIP', 'KKS', 'AKTA', 'KK', 'LAINNYA']);
            $table->string('nama_file', 255);
            $table->string('url', 255);
            $table->integer('ukuran_kb');
            $table->tinyInteger('valid')->default(0);
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
            
            $table->foreign('pendaftar_id')->references('id')->on('pendaftar');
            $table->index(['pendaftar_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftar_berkas');
    }
};