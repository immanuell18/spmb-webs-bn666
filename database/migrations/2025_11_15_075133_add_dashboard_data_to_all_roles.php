<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tidak perlu menambah kolom, hanya memastikan data sudah ada
        // Data pendaftar sudah otomatis masuk ke semua role melalui relasi
    }

    public function down(): void
    {
        // Nothing to rollback
    }
};