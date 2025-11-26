<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            // Tambah status akhir sesuai UKK
            if (!Schema::hasColumn('pendaftar', 'status_akhir')) {
                $table->enum('status_akhir', ['LULUS', 'TIDAK_LULUS', 'CADANGAN'])->nullable()->after('status');
            }
            
            // Pastikan biaya_pendaftaran ada
            if (!Schema::hasColumn('pendaftar', 'biaya_pendaftaran')) {
                $table->decimal('biaya_pendaftaran', 12, 2)->default(250000)->after('status_akhir');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn(['status_akhir', 'biaya_pendaftaran']);
        });
    }
};