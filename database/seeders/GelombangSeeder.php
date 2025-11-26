<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gelombang;

class GelombangSeeder extends Seeder
{
    public function run(): void
    {
        $gelombang = [
            [
                'nama' => 'Gelombang 1',
                'tahun' => 2025,
                'tgl_mulai' => '2025-01-01',
                'tgl_selesai' => '2025-03-31',
                'biaya_daftar' => 350000,
                'status' => 'aktif'
            ],
            [
                'nama' => 'Gelombang 2', 
                'tahun' => 2025,
                'tgl_mulai' => '2025-04-01',
                'tgl_selesai' => '2025-06-30',
                'biaya_daftar' => 350000,
                'status' => 'nonaktif'
            ],
            [
                'nama' => 'Gelombang 3',
                'tahun' => 2025,
                'tgl_mulai' => '2025-07-01', 
                'tgl_selesai' => '2025-08-31',
                'biaya_daftar' => 350000,
                'status' => 'nonaktif'
            ]
        ];

        foreach ($gelombang as $data) {
            Gelombang::updateOrCreate(
                ['nama' => $data['nama'], 'tahun' => $data['tahun']],
                $data
            );
        }
    }
}