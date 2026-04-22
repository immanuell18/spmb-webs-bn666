<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel provinces
        if (!Schema::hasTable('provinces')) {
            Schema::create('provinces', function (Blueprint $table) {
                $table->string('id', 10)->primary();
                $table->string('name', 100);
                $table->timestamps();
            });
        }

        // Tabel regencies (kabupaten/kota)
        if (!Schema::hasTable('regencies')) {
            Schema::create('regencies', function (Blueprint $table) {
                $table->string('id', 10)->primary();
                $table->string('province_id', 10);
                $table->string('name', 100);
                $table->timestamps();

                $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
                $table->index('province_id');
            });
        }

        // Tabel districts (kecamatan)
        if (!Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->string('id', 10)->primary();
                $table->string('regency_id', 10);
                $table->string('name', 100);
                $table->timestamps();

                $table->foreign('regency_id')->references('id')->on('regencies')->onDelete('cascade');
                $table->index('regency_id');
            });
        }

        // Tabel villages (kelurahan/desa)
        if (!Schema::hasTable('villages')) {
            Schema::create('villages', function (Blueprint $table) {
                $table->string('id', 15)->primary();
                $table->string('district_id', 10);
                $table->string('name', 100);
                $table->timestamps();

                $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
                $table->index('district_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};
