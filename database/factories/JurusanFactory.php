<?php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * JurusanFactory
 *
 * Contoh penggunaan:
 *   Jurusan::factory()->create();
 *   Jurusan::factory()->penuh()->create();
 *   Jurusan::factory(5)->create();
 */
class JurusanFactory extends Factory
{
    protected $model = Jurusan::class;

    /**
     * Daftar program keahlian SMK yang realistis.
     * Dibuat static agar tidak ada duplikat kode antar instance.
     */
    private static array $kodeTersedia = [
        'RPL'  => 'Rekayasa Perangkat Lunak',
        'TKJ'  => 'Teknik Komputer dan Jaringan',
        'MM'   => 'Multimedia',
        'AK'   => 'Akuntansi',
        'PM'   => 'Pemasaran',
        'AP'   => 'Administrasi Perkantoran',
        'TKR'  => 'Teknik Kendaraan Ringan',
        'TITL' => 'Teknik Instalasi Tenaga Listrik',
    ];

    public function definition(): array
    {
        // Ambil kode yang belum dipakai, fallback ke generate random
        $kode = fake()->unique()->randomElement(array_keys(self::$kodeTersedia));
        $nama = self::$kodeTersedia[$kode];

        return [
            'kode'      => $kode,
            'nama'      => $nama,
            'deskripsi' => "Program keahlian {$nama} yang mempersiapkan siswa untuk dunia kerja dan industri.",
            'kuota'     => fake()->randomElement([32, 36, 40, 48]),
        ];
    }

    /**
     * State: jurusan dengan kuota penuh (untuk test guard kuota).
     */
    public function penuh(): static
    {
        return $this->state(fn () => [
            'kuota' => 1, // Mudah di-fill, tinggal buat 1 pendaftar
        ]);
    }

    /**
     * State: jurusan dengan kuota besar.
     */
    public function kuotaBesar(): static
    {
        return $this->state(fn () => [
            'kuota' => 200,
        ]);
    }

    /**
     * State: jurusan RPL (sering dipakai di test).
     */
    public function rpl(): static
    {
        return $this->state(fn () => [
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
        ]);
    }

    /**
     * State: jurusan TKJ.
     */
    public function tkj(): static
    {
        return $this->state(fn () => [
            'kode' => 'TKJ',
            'nama' => 'Teknik Komputer dan Jaringan',
        ]);
    }
}
