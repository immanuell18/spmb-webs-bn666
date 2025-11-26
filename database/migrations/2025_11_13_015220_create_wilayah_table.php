<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('provinsi', 100);
            $table->string('kabupaten', 100);
            $table->string('kecamatan', 100);
            $table->string('kelurahan', 100);
            $table->string('kodepos', 10);
            $table->timestamps();
            
            $table->index(['kecamatan', 'kelurahan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};