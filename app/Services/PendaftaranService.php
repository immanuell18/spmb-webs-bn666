<?php

namespace App\Services;

use App\Models\Pendaftar;
use App\Models\PendaftarDataSiswa;
use App\Models\PendaftarDataOrtu;
use App\Models\PendaftarAsalSekolah;
use App\Models\Jurusan;
use App\Models\Gelombang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * PendaftaranService
 *
 * Bertanggung jawab atas semua logika bisnis pendaftaran siswa.
 * Controller hanya memanggil service ini dan meneruskan hasilnya ke view.
 *
 * Single Responsibility: hanya urusan proses pendaftaran.
 */
class PendaftaranService
{
    /**
     * Cek apakah gelombang valid dan masih aktif.
     */
    public function validasiGelombang(int $gelombangId): Gelombang
    {
        $gelombang = Gelombang::findOrFail($gelombangId);

        if (!$gelombang->isActive()) {
            throw new \RuntimeException('Gelombang pendaftaran tidak aktif atau sudah ditutup.');
        }

        return $gelombang;
    }

    /**
     * Cek apakah kuota jurusan masih tersedia.
     */
    public function validasiKuotaJurusan(int $jurusanId): Jurusan
    {
        $jurusan = Jurusan::withCount(['pendaftar' => fn($q) =>
            $q->whereIn('status', [Pendaftar::STATUS_SUBMIT, Pendaftar::STATUS_ADM_PASS, Pendaftar::STATUS_PAID])
        ])->findOrFail($jurusanId);

        if ($jurusan->pendaftar_count >= $jurusan->kuota) {
            throw new \RuntimeException(
                "Kuota jurusan {$jurusan->nama} sudah penuh! ({$jurusan->pendaftar_count}/{$jurusan->kuota})"
            );
        }

        return $jurusan;
    }

    /**
     * Proses pendaftaran lengkap dengan DB Transaction.
     * Kalau ada error di tengah jalan, semua data otomatis di-rollback.
     *
     * @return Pendaftar
     */
    public function prosesPendaftaran(User $user, Gelombang $gelombang, array $data): Pendaftar
    {
        return DB::transaction(function () use ($user, $gelombang, $data) {

            // Ambil atau buat record pendaftar
            $pendaftar = $this->getOrCreatePendaftar($user, $gelombang, $data);

            // Cek kalau sudah pernah lengkap — skip insert ulang
            if ($pendaftar->wasRecentlyCreated === false && $this->isPendaftaranLengkap($pendaftar)) {
                Log::info('Pendaftaran sudah lengkap sebelumnya', ['user_id' => $user->id]);
                return $pendaftar;
            }

            // Simpan data siswa
            PendaftarDataSiswa::updateOrCreate(
                ['pendaftar_id' => $pendaftar->id],
                [
                    'nik'        => $data['nik'],
                    'nisn'       => $data['nisn'] ?? null,
                    'nama'       => $data['nama_lengkap'],
                    'jk'         => $data['jenis_kelamin'],
                    'agama'      => $data['agama'],
                    'tmp_lahir'  => $data['tempat_lahir'],
                    'tgl_lahir'  => $data['tanggal_lahir'],
                    'alamat'     => $data['alamat'],
                    'wilayah_id' => null,
                    'lat'        => $data['latitude'] ?? null,
                    'lng'        => $data['longitude'] ?? null,
                ]
            );

            // Simpan data orang tua
            PendaftarDataOrtu::updateOrCreate(
                ['pendaftar_id' => $pendaftar->id],
                [
                    'nama_ayah'      => $data['nama_ayah'],
                    'pekerjaan_ayah' => $data['pekerjaan_ayah'],
                    'hp_ayah'        => $data['no_hp_ortu'],
                    'nama_ibu'       => $data['nama_ibu'],
                    'pekerjaan_ibu'  => $data['pekerjaan_ibu'],
                    'hp_ibu'         => $data['no_hp_ortu'],
                    'penghasilan'    => $data['penghasilan_ortu'],
                    'wali_nama'      => null,
                    'wali_hp'        => null,
                ]
            );

            // Simpan data asal sekolah
            PendaftarAsalSekolah::updateOrCreate(
                ['pendaftar_id' => $pendaftar->id],
                [
                    'npsn'         => $data['npsn'] ?? null,
                    'nama_sekolah' => $data['nama_sekolah'],
                    'kabupaten'    => $data['alamat_sekolah'],
                    'nilai_rata'   => $data['nilai_rata'] ?? 0,
                ]
            );

            Log::info('Pendaftaran berhasil disimpan', [
                'user_id'        => $user->id,
                'no_pendaftaran' => $pendaftar->no_pendaftaran,
            ]);

            return $pendaftar;
        });
    }

    /**
     * Cek apakah semua data pendaftaran sudah lengkap.
     */
    public function isPendaftaranLengkap(Pendaftar $pendaftar): bool
    {
        return PendaftarDataSiswa::where('pendaftar_id', $pendaftar->id)->exists()
            && PendaftarDataOrtu::where('pendaftar_id', $pendaftar->id)->exists()
            && PendaftarAsalSekolah::where('pendaftar_id', $pendaftar->id)->exists();
    }

    /**
     * Generate nomor pendaftaran unik.
     */
    public function generateNoPendaftaran(): string
    {
        $urutan = Pendaftar::withTrashed()->count() + 1;
        return 'SPMB' . now()->year . str_pad($urutan, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Kirim email konfirmasi pendaftaran (tidak gagalkan proses jika error).
     */
    public function kirimEmailKonfirmasi(User $user, Pendaftar $pendaftar): void
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\AccountActivationMail($user));
            Log::info('Email konfirmasi pendaftaran terkirim', ['email' => $user->email]);
        } catch (\Exception $e) {
            // Email gagal tidak boleh membatalkan pendaftaran
            Log::warning('Gagal kirim email konfirmasi: ' . $e->getMessage(), [
                'user_id'        => $user->id,
                'no_pendaftaran' => $pendaftar->no_pendaftaran,
            ]);
        }
    }

    // =============================================
    // Private Helpers
    // =============================================

    private function getOrCreatePendaftar(User $user, Gelombang $gelombang, array $data): Pendaftar
    {
        $existingPendaftar = Pendaftar::where('user_id', $user->id)->first();

        if ($existingPendaftar) {
            return $existingPendaftar;
        }

        return Pendaftar::create([
            'user_id'           => $user->id,
            'no_pendaftaran'    => $this->generateNoPendaftaran(),
            'nama'              => $data['nama_lengkap'],
            'email'             => $user->email,
            'jurusan_id'        => $data['jurusan_id'],
            'gelombang_id'      => $data['gelombang_id'],
            'biaya_pendaftaran' => $gelombang->biaya_daftar,
            'tanggal_daftar'    => now(),
            'status'            => Pendaftar::STATUS_SUBMIT,
        ]);
    }
}
