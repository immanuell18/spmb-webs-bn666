<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            $table->dropForeign(['wilayah_id']);
            $table->unsignedBigInteger('wilayah_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('wilayah_id')->nullable(false)->change();
            $table->foreign('wilayah_id')->references('id')->on('wilayah');
        });
    }
};