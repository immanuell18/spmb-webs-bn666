<?php

namespace Database\Factories;

use App\Models\Pendaftar;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Gelombang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk menghasilkan data dummy pendaftar buat testing.
 * Contoh penggunaan:
 *   Pendaftar::factory(10)->create();
 *   Pendaftar::factory()->paid()->create();
 *   Pendaftar::factory()->rejected()->create();
 */
class PendaftarFactory extends Factory
{
    protected $model = Pendaftar::class;

    public function definition(): array
    {
        $user     = User::factory()->create(['role' => 'pendaftar']);

        // Pakai factory jika tidak ada data — tidak bergantung seeder
        $jurusan  = Jurusan::inRandomOrder()->first()
            ?? Jurusan::factory()->create();
        $gelombang = Gelombang::where('status', 'aktif')->inRandomOrder()->first()
            ?? Gelombang::factory()->aktif()->create();

        return [
            'user_id'             => $user->id,
            'no_pendaftaran'      => 'SPMB' . now()->year . str_pad(
                                        fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT
                                    ),
            'nama'                => fake('id_ID')->name(),
            'email'               => $user->email,
            'jurusan_id'          => $jurusan->id,
            'gelombang_id'        => $gelombang->id,
            'biaya_pendaftaran'   => $gelombang->biaya_daftar ?? 500000,
            'tanggal_daftar'      => fake()->dateTimeBetween('-3 months', 'now'),
            'status'              => Pendaftar::STATUS_SUBMIT,
            'status_akhir'        => null,
            'catatan_admin'       => null,
            'tgl_verifikasi_adm'  => null,
            'user_verifikasi_adm' => null,
        ];
    }

    // State: sudah lulus administrasi, boleh bayar
    public function admPass(): static
    {
        return $this->state(fn () => [
            'status'             => Pendaftar::STATUS_ADM_PASS,
            'tgl_verifikasi_adm' => now()->subHours(fake()->numberBetween(1, 48)),
            'user_verifikasi_adm' => 'Admin SPMB',
        ]);
    }

    // State: berkas ditolak
    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'             => Pendaftar::STATUS_ADM_REJECT,
            'tgl_verifikasi_adm' => now()->subDays(1),
            'user_verifikasi_adm' => 'Admin SPMB',
            'catatan_admin'      => 'Berkas tidak lengkap atau tidak sesuai.',
        ]);
    }

    // State: sudah bayar, menunggu pengumuman
    public function paid(): static
    {
        return $this->state(fn () => [
            'status'              => Pendaftar::STATUS_PAID,
            'tgl_verifikasi_adm'  => now()->subDays(3),
            'user_verifikasi_adm' => 'Admin SPMB',
            'tanggal_pembayaran'  => now()->subDays(2),
        ]);
    }

    // State: sudah ada keputusan akhir (lulus/tidak/cadangan)
    public function announced(string $hasilAkhir = 'LULUS'): static
    {
        return $this->paid()->state(fn () => [
            'status_akhir'    => $hasilAkhir,
            'tgl_pengumuman'  => now()->subDay(),
            'user_pengumuman' => 'Kepala Sekolah',
        ]);
    }
}
