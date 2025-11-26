<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        User::updateOrCreate(
            ['email' => 'admbn666@sch.id'],
            [
                'name' => 'Admin SPMB',
                'email' => 'admbn666@sch.id',
                'hp' => '081234567890',
                'password' => Hash::make('AdminBn666'),
                'role' => 'admin',
                'aktif' => 1,
                'email_verified_at' => now()
            ]
        );

        // Bagian Keuangan
        User::updateOrCreate(
            ['email' => 'keuanganbn666@sch.id'],
            [
                'name' => 'Keuangan SPMB',
                'email' => 'keuanganbn666@sch.id',
                'hp' => '081234567891',
                'password' => Hash::make('KUBN666'),
                'role' => 'keuangan',
                'aktif' => 1,
                'email_verified_at' => now()
            ]
        );

        // Kepala Sekolah
        User::updateOrCreate(
            ['email' => 'yayasanbn666@sch.id'],
            [
                'name' => 'Kepala Sekolah',
                'email' => 'yayasanbn666@sch.id',
                'hp' => '081234567892',
                'password' => Hash::make('yayasanbaknus666'),
                'role' => 'kepsek',
                'aktif' => 1,
                'email_verified_at' => now()
            ]
        );

        // Verifikator Administrasi
        User::updateOrCreate(
            ['email' => 'verifikator@sch.id'],
            [
                'name' => 'Verifikator Administrasi',
                'email' => 'verifikator@sch.id',
                'hp' => '081234567893',
                'password' => Hash::make('verifikator123'),
                'role' => 'verifikator_adm',
                'aktif' => 1,
                'email_verified_at' => now()
            ]
        );
    }
}