<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftar', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable();
            }
            if (!Schema::hasColumn('pendaftar', 'tanggal_pembayaran')) {
                $table->datetime('tanggal_pembayaran')->nullable();
            }
            if (!Schema::hasColumn('pendaftar', 'tanggal_kelulusan')) {
                $table->datetime('tanggal_kelulusan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropColumn(['catatan_admin', 'tanggal_pembayaran', 'tanggal_kelulusan']);
        });
    }
};