<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            // Standardize status enum sesuai UKK
            $table->enum('status', ['SUBMIT', 'ADM_PASS', 'ADM_REJECT', 'PAID', 'LULUS', 'TIDAK_LULUS', 'CADANGAN'])
                  ->default('SUBMIT')
                  ->change();
            
            // Remove redundant status fields
            $table->dropColumn([
                'status_berkas',
                'status_verifikasi', 
                'status_pembayaran',
                'catatan_verifikasi',
                'catatan_pembayaran',
                'tanggal_verifikasi',
                'tanggal_validasi_bayar',
                'bukti_bayar'
            ]);
            
            // Add standardized fields
            $table->text('catatan_admin')->nullable()->after('status');
            $table->timestamp('tanggal_verifikasi')->nullable()->after('catatan_admin');
            $table->timestamp('tanggal_pembayaran')->nullable()->after('tanggal_verifikasi');
            $table->timestamp('tanggal_kelulusan')->nullable()->after('tanggal_pembayaran');
        });
        
        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            // Ensure coordinate fields exist with proper validation
            if (!Schema::hasColumn('pendaftar_data_siswa', 'lat')) {
                $table->decimal('lat', 10, 8)->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('pendaftar_data_siswa', 'lng')) {
                $table->decimal('lng', 11, 8)->nullable()->after('lat');
            }
            
            // Add coordinate validation fields
            $table->boolean('koordinat_valid')->default(false)->after('lng');
            $table->string('sumber_koordinat')->nullable()->after('koordinat_valid'); // GPS, GEOCODING, MANUAL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            // Restore old status system
            $table->enum('status', ['SUBMIT', 'ADM_PASS', 'ADM_REJECT', 'PAID'])->change();
            
            // Restore old fields
            $table->string('status_berkas')->nullable();
            $table->string('status_verifikasi')->nullable();
            $table->string('status_pembayaran')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->text('catatan_pembayaran')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamp('tanggal_validasi_bayar')->nullable();
            $table->string('bukti_bayar')->nullable();
            
            // Remove new fields
            $table->dropColumn([
                'catatan_admin',
                'tanggal_verifikasi',
                'tanggal_pembayaran', 
                'tanggal_kelulusan'
            ]);
        });
        
        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            $table->dropColumn([
                'koordinat_valid',
                'sumber_koordinat'
            ]);
        });
    }
};
