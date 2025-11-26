<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusan = [
            [
                'kode' => 'PPLG',
                'nama' => 'Pengembangan Perangkat Lunak dan Gim',
                'deskripsi' => 'Program keahlian yang mempelajari pengembangan aplikasi, website, game, dan sistem informasi menggunakan teknologi terkini.',
                'kurikulum' => json_encode([
                    'Pemrograman Dasar',
                    'Basis Data',
                    'Pemrograman Web',
                    'Pemrograman Mobile',
                    'Game Development',
                    'UI/UX Design',
                    'DevOps'
                ]),
                'kuota' => 72
            ],
            [
                'kode' => 'AKT',
                'nama' => 'Akuntansi dan Keuangan Lembaga',
                'deskripsi' => 'Program keahlian yang mempelajari pencatatan, pengelolaan, dan pelaporan keuangan perusahaan serta lembaga.',
                'kurikulum' => json_encode([
                    'Pengantar Akuntansi',
                    'Akuntansi Perusahaan Jasa',
                    'Akuntansi Perusahaan Dagang',
                    'Akuntansi Perusahaan Manufaktur',
                    'Perpajakan',
                    'Audit',
                    'Sistem Informasi Akuntansi'
                ]),
                'kuota' => 36
            ],
            [
                'kode' => 'DKV',
                'nama' => 'Desain Komunikasi Visual',
                'deskripsi' => 'Program keahlian yang mempelajari desain grafis, branding, advertising, dan komunikasi visual untuk berbagai media.',
                'kurikulum' => json_encode([
                    'Dasar Desain Grafis',
                    'Tipografi',
                    'Fotografi',
                    'Ilustrasi Digital',
                    'Branding & Identity',
                    'Advertising Design',
                    'Motion Graphics'
                ]),
                'kuota' => 36
            ],
            [
                'kode' => 'ANM',
                'nama' => 'Animasi',
                'deskripsi' => 'Program keahlian yang mempelajari pembuatan animasi 2D, 3D, motion graphics, dan visual effects untuk film dan game.',
                'kurikulum' => json_encode([
                    'Dasar Animasi',
                    'Animasi 2D',
                    'Animasi 3D',
                    'Character Design',
                    'Storyboard',
                    'Visual Effects',
                    'Post Production'
                ]),
                'kuota' => 36
            ],
            [
                'kode' => 'BDP',
                'nama' => 'Bisnis Daring dan Pemasaran',
                'deskripsi' => 'Program keahlian yang mempelajari strategi pemasaran digital, e-commerce, dan pengelolaan bisnis online.',
                'kurikulum' => json_encode([
                    'Pengantar Bisnis',
                    'Digital Marketing',
                    'E-Commerce',
                    'Social Media Marketing',
                    'Content Marketing',
                    'SEO & SEM',
                    'Marketplace Management'
                ]),
                'kuota' => 36
            ]
        ];

        foreach ($jurusan as $data) {
            Jurusan::updateOrCreate(
                ['kode' => $data['kode']],
                $data
            );
        }
    }
}