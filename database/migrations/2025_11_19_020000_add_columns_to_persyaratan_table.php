<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persyaratan', function (Blueprint $table) {
            if (!Schema::hasColumn('persyaratan', 'nama')) {
                $table->string('nama', 255)->after('id');
            }
            if (!Schema::hasColumn('persyaratan', 'deskripsi')) {
                $table->string('deskripsi', 500)->nullable()->after('nama');
            }
            if (!Schema::hasColumn('persyaratan', 'jenis')) {
                $table->enum('jenis', ['dokumen', 'foto', 'sertifikat'])->default('dokumen')->after('deskripsi');
            }
            if (!Schema::hasColumn('persyaratan', 'wajib')) {
                $table->boolean('wajib')->default(true)->after('jenis');
            }
            if (!Schema::hasColumn('persyaratan', 'urutan')) {
                $table->integer('urutan')->default(1)->after('wajib');
            }
        });
    }

    public function down(): void
    {
        Schema::table('persyaratan', function (Blueprint $table) {
            $table->dropColumn(['nama', 'deskripsi', 'jenis', 'wajib', 'urutan']);
        });
    }
};
