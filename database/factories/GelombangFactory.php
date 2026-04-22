<?php

namespace Database\Factories;

use App\Models\Gelombang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * GelombangFactory
 *
 * Contoh penggunaan:
 *   Gelombang::factory()->create();
 *   Gelombang::factory()->aktif()->create();
 *   Gelombang::factory()->nonaktif()->create();
 *   Gelombang::factory()->sudahBerakhir()->create();
 *   Gelombang::factory()->belumMulai()->create();
 */
class GelombangFactory extends Factory
{
    protected $model = Gelombang::class;

    public function definition(): array
    {
        $tahun    = fake()->randomElement([now()->year, now()->year + 1]);
        $tglMulai = fake()->dateTimeBetween('-2 months', '+1 month');
        $tglSelesai = (clone $tglMulai)->modify('+30 days');

        return [
            'nama'         => 'Gelombang ' . fake()->randomElement(['I', 'II', 'III']),
            'tahun'        => $tahun,
            'tgl_mulai'    => $tglMulai->format('Y-m-d'),
            'tgl_selesai'  => $tglSelesai->format('Y-m-d'),
            'biaya_daftar' => fake()->randomElement([250000, 350000, 500000, 750000]),
            'status'       => fake()->randomElement(['aktif', 'nonaktif']),
        ];
    }

    // ================================================================
    // Status States
    // ================================================================

    /**
     * State: gelombang aktif DAN sedang dalam rentang tanggal.
     * Ini yang paling sering dipakai di test — gelombang yang
     * bisa dipakai untuk mendaftar sekarang.
     */
    public function aktif(): static
    {
        return $this->state(fn () => [
            'status'      => 'aktif',
            'tgl_mulai'   => now()->subDays(5)->format('Y-m-d'),
            'tgl_selesai' => now()->addDays(25)->format('Y-m-d'),
        ]);
    }

    /**
     * State: gelombang nonaktif.
     */
    public function nonaktif(): static
    {
        return $this->state(fn () => [
            'status'      => 'nonaktif',
            'tgl_mulai'   => now()->subDays(10)->format('Y-m-d'),
            'tgl_selesai' => now()->addDays(20)->format('Y-m-d'),
        ]);
    }

    // ================================================================
    // Tanggal States
    // ================================================================

    /**
     * State: gelombang sudah selesai (tanggal lewat).
     * Berguna untuk test "tidak bisa daftar di gelombang yang sudah tutup".
     */
    public function sudahBerakhir(): static
    {
        return $this->state(fn () => [
            'status'      => 'nonaktif',
            'tgl_mulai'   => now()->subDays(60)->format('Y-m-d'),
            'tgl_selesai' => now()->subDays(30)->format('Y-m-d'),
        ]);
    }

    /**
     * State: gelombang belum mulai.
     * Berguna untuk test "tidak bisa daftar sebelum waktunya".
     */
    public function belumMulai(): static
    {
        return $this->state(fn () => [
            'status'      => 'aktif',
            'tgl_mulai'   => now()->addDays(10)->format('Y-m-d'),
            'tgl_selesai' => now()->addDays(40)->format('Y-m-d'),
        ]);
    }

    // ================================================================
    // Biaya States
    // ================================================================

    /**
     * State: gelombang gratis (biaya_daftar = 0).
     * Berguna untuk test kalkulasi biaya.
     */
    public function gratis(): static
    {
        return $this->state(fn () => [
            'biaya_daftar' => 0,
        ]);
    }

    /**
     * State: gelombang dengan biaya tertentu.
     */
    public function denganBiaya(int $biaya): static
    {
        return $this->state(fn () => [
            'biaya_daftar' => $biaya,
        ]);
    }

    // ================================================================
    // Named States (shortcut untuk test spesifik)
    // ================================================================

    /**
     * Gelombang I — shortcut yang sering dipakai.
     */
    public function gelombangSatu(): static
    {
        return $this->aktif()->state(fn () => [
            'nama'  => 'Gelombang I',
            'tahun' => now()->year,
        ]);
    }

    /**
     * Gelombang II — untuk test multi-gelombang.
     */
    public function gelombangDua(): static
    {
        return $this->aktif()->state(fn () => [
            'nama'  => 'Gelombang II',
            'tahun' => now()->year,
        ]);
    }
}
