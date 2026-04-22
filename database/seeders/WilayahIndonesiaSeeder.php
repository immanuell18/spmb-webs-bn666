<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WilayahIndonesiaSeeder extends Seeder
{
    /**
     * Import data wilayah Indonesia dari file CSV ke database.
     * CSV ada di database/library/
     */
    public function run(): void
    {
        $this->command->info('Mulai import data wilayah Indonesia...');

        $this->importProvinces();
        $this->importRegencies();
        $this->importDistricts();
        $this->importVillages();

        $this->command->info('✅ Import data wilayah selesai!');
    }

    private function importProvinces(): void
    {
        $file = database_path('library/provinces.csv');

        if (!file_exists($file)) {
            $this->command->warn('File provinces.csv tidak ditemukan, skip.');
            return;
        }

        // Skip jika data sudah ada
        if (DB::table('provinces')->count() > 0) {
            $this->command->info('Tabel provinces sudah berisi data, skip.');
            return;
        }

        $this->command->info('Importing provinces...');
        $now = now();
        $data = [];

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 2) continue;
                $data[] = [
                    'id'         => trim($row[0]),
                    'name'       => trim($row[1]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // Insert per 100 baris
                if (count($data) >= 100) {
                    DB::table('provinces')->insertOrIgnore($data);
                    $data = [];
                }
            }
            fclose($handle);
        }

        if (!empty($data)) {
            DB::table('provinces')->insertOrIgnore($data);
        }

        $total = DB::table('provinces')->count();
        $this->command->info("  → {$total} provinsi berhasil diimport.");
    }

    private function importRegencies(): void
    {
        $file = database_path('library/regencies.csv');

        if (!file_exists($file)) {
            $this->command->warn('File regencies.csv tidak ditemukan, skip.');
            return;
        }

        if (DB::table('regencies')->count() > 0) {
            $this->command->info('Tabel regencies sudah berisi data, skip.');
            return;
        }

        $this->command->info('Importing regencies (kabupaten/kota)...');
        $now = now();
        $data = [];

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue;
                $data[] = [
                    'id'          => trim($row[0]),
                    'province_id' => trim($row[1]),
                    'name'        => trim($row[2]),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];

                if (count($data) >= 500) {
                    DB::table('regencies')->insertOrIgnore($data);
                    $data = [];
                }
            }
            fclose($handle);
        }

        if (!empty($data)) {
            DB::table('regencies')->insertOrIgnore($data);
        }

        $total = DB::table('regencies')->count();
        $this->command->info("  → {$total} kabupaten/kota berhasil diimport.");
    }

    private function importDistricts(): void
    {
        $file = database_path('library/districts.csv');

        if (!file_exists($file)) {
            $this->command->warn('File districts.csv tidak ditemukan, skip.');
            return;
        }

        if (DB::table('districts')->count() > 0) {
            $this->command->info('Tabel districts sudah berisi data, skip.');
            return;
        }

        $this->command->info('Importing districts (kecamatan)...');
        $now = now();
        $data = [];
        $count = 0;

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue;
                $data[] = [
                    'id'         => trim($row[0]),
                    'regency_id' => trim($row[1]),
                    'name'       => trim($row[2]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($data) >= 500) {
                    DB::table('districts')->insertOrIgnore($data);
                    $count += count($data);
                    $data = [];
                    $this->command->info("  → {$count} kecamatan terimport...");
                }
            }
            fclose($handle);
        }

        if (!empty($data)) {
            DB::table('districts')->insertOrIgnore($data);
        }

        $total = DB::table('districts')->count();
        $this->command->info("  → Total {$total} kecamatan berhasil diimport.");
    }

    private function importVillages(): void
    {
        $file = database_path('library/villages.csv');

        if (!file_exists($file)) {
            $this->command->warn('File villages.csv tidak ditemukan, skip.');
            return;
        }

        if (DB::table('villages')->count() > 0) {
            $this->command->info('Tabel villages sudah berisi data, skip.');
            return;
        }

        $this->command->info('Importing villages (kelurahan/desa) - ini butuh beberapa menit...');
        $now = now();
        $data = [];
        $count = 0;

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue;
                $data[] = [
                    'id'          => trim($row[0]),
                    'district_id' => trim($row[1]),
                    'name'        => trim($row[2]),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];

                if (count($data) >= 1000) {
                    DB::table('villages')->insertOrIgnore($data);
                    $count += count($data);
                    $data = [];
                    $this->command->info("  → {$count} kelurahan terimport...");
                }
            }
            fclose($handle);
        }

        if (!empty($data)) {
            DB::table('villages')->insertOrIgnore($data);
        }

        $total = DB::table('villages')->count();
        $this->command->info("  → Total {$total} kelurahan/desa berhasil diimport.");
    }
}
