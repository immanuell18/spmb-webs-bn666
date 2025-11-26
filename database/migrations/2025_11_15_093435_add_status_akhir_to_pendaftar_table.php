<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->enum('status_akhir', ['LULUS', 'TIDAK_LULUS', 'CADANGAN'])->nullable()->after('status');
            $table->datetime('tgl_pengumuman')->nullable()->after('status_akhir');
            $table->string('user_pengumuman', 100)->nullable()->after('tgl_pengumuman');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn(['status_akhir', 'tgl_pengumuman', 'user_pengumuman']);
        });
    }
};