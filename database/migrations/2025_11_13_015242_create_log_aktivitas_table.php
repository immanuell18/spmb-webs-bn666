<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('aksi', 100);
            $table->string('objek', 100);
            $table->json('objek_data');
            $table->datetime('waktu');
            $table->string('ip', 45);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('pengguna');
            $table->index(['user_id', 'waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};