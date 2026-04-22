<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * WilayahApiController
 *
 * Proxy ke API wilayah Indonesia dari emsifa:
 * https://github.com/emsifa/api-wilayah-indonesia
 *
 * Endpoint source:
 *   Provinsi   → https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json
 *   Kabupaten  → https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{id}.json
 *   Kecamatan  → https://emsifa.github.io/api-wilayah-indonesia/api/districts/{id}.json
 *   Kelurahan  → https://emsifa.github.io/api-wilayah-indonesia/api/villages/{id}.json
 *
 * Semua response di-cache di Laravel selama 24 jam agar:
 *   1. Tidak ada lag dari API eksternal saat user ngetik
 *   2. Tidak bergantung koneksi internet setelah pertama kali load
 *   3. Tidak kena rate limit API eksternal
 */
class WilayahApiController extends Controller
{
    private const BASE_URL  = 'https://emsifa.github.io/api-wilayah-indonesia/api';
    private const CACHE_TTL = 60 * 60 * 24; // 24 jam (data wilayah jarang berubah)

    /**
     * GET /api/wilayah/provinsi
     * Ambil semua provinsi Indonesia.
     */
    public function getProvinsi()
    {
        $data = Cache::remember('wilayah.provinsi', self::CACHE_TTL, function () {
            return $this->fetchFromApi('provinces.json');
        });

        return response()->json($data);
    }

    /**
     * GET /api/wilayah/kabupaten/{provinsiId}
     * Ambil kabupaten/kota berdasarkan ID provinsi.
     */
    public function getKabupaten($provinsiId)
    {
        // Validasi: id provinsi harus 2 digit angka
        if (!preg_match('/^\d{2}$/', $provinsiId)) {
            return response()->json(['error' => 'ID provinsi tidak valid'], 422);
        }

        $cacheKey = "wilayah.kabupaten.{$provinsiId}";

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($provinsiId) {
            return $this->fetchFromApi("regencies/{$provinsiId}.json");
        });

        return response()->json($data);
    }

    /**
     * GET /api/wilayah/kecamatan/{provinsiId}/{kabupatenId}
     * Ambil kecamatan berdasarkan ID kabupaten.
     */
    public function getKecamatan($provinsiId, $kabupatenId)
    {
        if (!preg_match('/^\d{4}$/', $kabupatenId)) {
            return response()->json(['error' => 'ID kabupaten tidak valid'], 422);
        }

        // Ambil dari DB lokal
        $districts = \App\Models\District::where('regency_id', $kabupatenId)
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($districts->isNotEmpty()) {
            return response()->json($districts);
        }

        // Fallback ke API emsifa
        $cacheKey = "wilayah.kecamatan.{$kabupatenId}";
        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($kabupatenId) {
            return $this->fetchFromApi("districts/{$kabupatenId}.json");
        });

        return response()->json($data);
    }

    /**
     * GET /api/wilayah/kelurahan/{provinsiId}/{kabupatenId}/{kecamatanId}
     * Ambil kelurahan/desa berdasarkan ID kecamatan.
     */
    public function getKelurahan($provinsiId, $kabupatenId, $kecamatanId)
    {
        if (!preg_match('/^\d{6,7}$/', $kecamatanId)) {
            return response()->json(['error' => 'ID kecamatan tidak valid'], 422);
        }

        // Ambil dari DB lokal karena ID kecamatan 7 digit tidak cocok dengan API emsifa
        $villages = \App\Models\Village::where('district_id', $kecamatanId)
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($villages->isNotEmpty()) {
            return response()->json($villages);
        }

        // Fallback ke API emsifa jika tidak ada di DB
        $cacheKey = "wilayah.kelurahan.{$kecamatanId}";
        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($kecamatanId) {
            return $this->fetchFromApi("villages/{$kecamatanId}.json");
        });

        return response()->json($data);
    }

    /**
     * GET /api/wilayah/search?q=...
     * Cari wilayah berdasarkan nama kecamatan/kabupaten.
     * (Digunakan pada autocomplete form pendaftaran)
     */
    public function searchWilayah(Request $request)
    {
        $search = strtolower(trim($request->get('q', '')));

        if (strlen($search) < 3) {
            return response()->json([]);
        }

        // Ambil semua provinsi
        $provinces = Cache::remember('wilayah.provinsi', self::CACHE_TTL, function () {
            return $this->fetchFromApi('provinces.json');
        });

        // Cari di semua provinsi → tidak efisien untuk production,
        // tapi untuk scope SPMB ini cukup (siswa biasanya sudah tahu provinsinya)
        // Lebih baik: arahkan user isi provinsi dulu → dropdown cascade
        return response()->json([
            'message' => 'Gunakan dropdown cascade: pilih Provinsi → Kabupaten → Kecamatan',
            'provinces' => $provinces,
        ]);
    }

    /**
     * Prefetch & cache seluruh data wilayah satu provinsi sekaligus.
     * Bisa dipanggil via: php artisan wilayah:prefetch
     * Route: GET /api/wilayah/prefetch/{provinsiId} (admin only)
     */
    public function prefetchProvinsi($provinsiId)
    {
        // Hanya bisa diakses admin
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $kabupatenList = $this->fetchFromApi("regencies/{$provinsiId}.json");
        Cache::put("wilayah.kabupaten.{$provinsiId}", $kabupatenList, self::CACHE_TTL);

        $totalKecamatan = 0;
        foreach ($kabupatenList as $kab) {
            $kecamatanList = $this->fetchFromApi("districts/{$kab['id']}.json");
            Cache::put("wilayah.kecamatan.{$kab['id']}", $kecamatanList, self::CACHE_TTL);
            $totalKecamatan += count($kecamatanList);
        }

        Log::info('[Wilayah] Prefetch selesai', [
            'provinsi_id'     => $provinsiId,
            'jumlah_kabupaten' => count($kabupatenList),
            'jumlah_kecamatan' => $totalKecamatan,
        ]);

        return response()->json([
            'success'          => true,
            'provinsi_id'      => $provinsiId,
            'jumlah_kabupaten' => count($kabupatenList),
            'jumlah_kecamatan' => $totalKecamatan,
        ]);
    }

    // =============================================
    // Private Helper
    // =============================================

    /**
     * Fetch JSON dari emsifa API dengan fallback jika gagal.
     */
    private function fetchFromApi(string $endpoint): array
    {
        try {
            $url      = self::BASE_URL . '/' . $endpoint;
            $response = Http::timeout(10)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($url);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::warning("[Wilayah API] Response tidak OK: {$url}", [
                'status' => $response->status(),
            ]);

            return [];

        } catch (\Exception $e) {
            Log::error("[Wilayah API] Gagal fetch: {$endpoint}", [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}