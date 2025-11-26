<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add sample coordinates for demo purposes
        $sampleCoordinates = [
            // Jakarta area coordinates
            [-6.2088, 106.8456], // Jakarta Pusat
            [-6.1751, 106.8650], // Jakarta Timur
            [-6.2297, 106.7663], // Jakarta Barat
            [-6.2615, 106.8106], // Jakarta Selatan
            [-6.1384, 106.8759], // Jakarta Utara
            [-6.3667, 106.8333], // Depok
            [-6.4025, 106.7942], // Bogor
            [-6.1200, 106.6500], // Tangerang
            [-6.2382, 106.9756], // Bekasi
            [-6.5971, 106.8060], // Cibinong
        ];

        // Update existing pendaftar_data_siswa with random coordinates
        $pendaftarDataSiswa = DB::table('pendaftar_data_siswa')
            ->whereNull('lat')
            ->orWhereNull('lng')
            ->get();

        foreach ($pendaftarDataSiswa as $index => $data) {
            $coordIndex = $index % count($sampleCoordinates);
            $baseCoord = $sampleCoordinates[$coordIndex];
            
            // Add small random offset for variety
            $lat = $baseCoord[0] + (rand(-100, 100) / 10000); // ±0.01 degree
            $lng = $baseCoord[1] + (rand(-100, 100) / 10000); // ±0.01 degree
            
            DB::table('pendaftar_data_siswa')
                ->where('pendaftar_id', $data->pendaftar_id)
                ->update([
                    'lat' => $lat,
                    'lng' => $lng
                ]);
        }
    }

    public function down(): void
    {
        // Reset coordinates
        DB::table('pendaftar_data_siswa')->update([
            'lat' => null,
            'lng' => null
        ]);
    }
};