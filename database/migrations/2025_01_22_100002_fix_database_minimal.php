<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom di users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'hp')) {
                $table->string('hp', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'aktif')) {
                $table->tinyInteger('aktif')->default(1)->after('role');
            }
        });

        // 2. Update role enum di users
        DB::table('users')->where('role', 'calon_siswa')->update(['role' => 'pendaftar']);
        DB::table('users')->where('role', 'verifikator')->update(['role' => 'verifikator_adm']);

        // 3. Tambah kolom agama di pendaftar_data_siswa
        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftar_data_siswa', 'agama')) {
                $table->string('agama', 20)->nullable()->after('jk');
            }
        });

        // 4. Tambah kolom status di gelombang
        Schema::table('gelombang', function (Blueprint $table) {
            if (!Schema::hasColumn('gelombang', 'status')) {
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('biaya_daftar');
            }
        });

        // 5. Tambah kolom status_akhir di pendaftar
        Schema::table('pendaftar', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftar', 'status_akhir')) {
                $table->enum('status_akhir', ['LULUS', 'TIDAK_LULUS', 'CADANGAN'])->nullable()->after('status');
            }
            if (!Schema::hasColumn('pendaftar', 'tgl_pengumuman')) {
                $table->datetime('tgl_pengumuman')->nullable()->after('status_akhir');
            }
            if (!Schema::hasColumn('pendaftar', 'user_pengumuman')) {
                $table->string('user_pengumuman', 100)->nullable()->after('tgl_pengumuman');
            }
        });

        // 6. Update enum values (hati-hati)
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pendaftar','admin','verifikator_adm','keuangan','kepsek') NOT NULL");
        } catch (Exception $e) {
            // Ignore jika gagal
        }

        try {
            DB::statement("ALTER TABLE pendaftar_berkas MODIFY COLUMN jenis ENUM('IJAZAH','RAPOR','KIP','KKS','AKTA','KK','BUKTI_BAYAR','LAINNYA') NOT NULL");
        } catch (Exception $e) {
            // Ignore jika gagal
        }

        try {
            DB::statement("ALTER TABLE pendaftar MODIFY COLUMN status ENUM('SUBMIT','ADM_PASS','ADM_REJECT','PAID') NOT NULL DEFAULT 'SUBMIT'");
        } catch (Exception $e) {
            // Ignore jika gagal
        }
    }

    public function down(): void
    {
        // Minimal rollback
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'hp')) {
                $table->dropColumn('hp');
            }
            if (Schema::hasColumn('users', 'aktif')) {
                $table->dropColumn('aktif');
            }
        });

        Schema::table('pendaftar_data_siswa', function (Blueprint $table) {
            if (Schema::hasColumn('pendaftar_data_siswa', 'agama')) {
                $table->dropColumn('agama');
            }
        });
    }
};