<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        DB::table('jurusan')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Insert the 5 specific majors
        DB::table('jurusan')->insert([
            [
                'kode' => 'PPLG',
                'nama' => 'Pengembangan Perangkat Lunak dan Gim',
                'kuota' => 36,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode' => 'AKT',
                'nama' => 'Akuntansi dan Keuangan Lembaga',
                'kuota' => 36,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode' => 'ANM',
                'nama' => 'Animasi',
                'kuota' => 36,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode' => 'DKV',
                'nama' => 'Desain Komunikasi Visual',
                'kuota' => 36,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode' => 'BDP',
                'nama' => 'Broadcasting dan Perfilman',
                'kuota' => 36,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('jurusan')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};