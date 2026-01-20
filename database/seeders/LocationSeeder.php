<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Import provinces
        $provincesFile = database_path('library/provinces.csv');
        if (file_exists($provincesFile)) {
            $provinces = array_map('str_getcsv', file($provincesFile));
            foreach ($provinces as $province) {
                if (count($province) >= 2) {
                    DB::table('provinces')->insertOrIgnore([
                        'code' => $province[0],
                        'name' => $province[1],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Import regencies
        $regenciesFile = database_path('library/regencies.csv');
        if (file_exists($regenciesFile)) {
            $regencies = array_map('str_getcsv', file($regenciesFile));
            foreach ($regencies as $regency) {
                if (count($regency) >= 3) {
                    DB::table('regencies')->insertOrIgnore([
                        'code' => $regency[0],
                        'province_code' => $regency[1],
                        'name' => $regency[2],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Import districts
        $districtsFile = database_path('library/districts.csv');
        if (file_exists($districtsFile)) {
            $districts = array_map('str_getcsv', file($districtsFile));
            foreach ($districts as $district) {
                if (count($district) >= 3) {
                    DB::table('districts')->insertOrIgnore([
                        'code' => $district[0],
                        'regency_code' => $district[1],
                        'name' => $district[2],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Import villages
        $villagesFile = database_path('library/villages.csv');
        if (file_exists($villagesFile)) {
            $villages = array_map('str_getcsv', file($villagesFile));
            foreach ($villages as $village) {
                if (count($village) >= 3) {
                    DB::table('villages')->insertOrIgnore([
                        'code' => $village[0],
                        'district_code' => $village[1],
                        'name' => $village[2],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}