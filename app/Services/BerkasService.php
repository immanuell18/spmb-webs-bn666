<?php

namespace App\Services;

use App\Models\Pendaftar;
use App\Models\PendaftarBerkas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * BerkasService
 *
 * Mengelola logika upload, delete, dan validasi berkas pendaftar.
 * SRP: hanya urusan manajemen file berkas.
 */
class BerkasService
{
    /**
     * Map jenis berkas dari form ke format database.
     */
    private const JENIS_MAP = [
        'ijazah' => 'IJAZAH',
        'rapor'  => 'RAPOR',
        'kip'    => 'KIP',
        'kks'    => 'KKS',
        'akta'   => 'AKTA',
        'kk'     => 'KK',
        'foto'   => 'LAINNYA',
    ];

    public const BERKAS_WAJIB_MINIMAL = 4;

    /**
     * Upload berkas dan simpan ke DB.
     * Otomatis menghapus berkas lama dengan jenis yang sama.
     *
     * @return PendaftarBerkas
     */
    public function upload(Pendaftar $pendaftar, string $jenisBerkas, UploadedFile $file): PendaftarBerkas
    {
        $jenis    = self::JENIS_MAP[$jenisBerkas];
        $fileName = $this->generateFileName($pendaftar->no_pendaftaran, $jenisBerkas, $file);
        $filePath = $file->storeAs('berkas', $fileName, 'public');

        // Hapus berkas lama (replace)
        $this->deleteLama($pendaftar->id, $jenis);

        $berkas = PendaftarBerkas::create([
            'pendaftar_id' => $pendaftar->id,
            'jenis'        => $jenis,
            'nama_file'    => $fileName,
            'url'          => $filePath,
            'ukuran_kb'    => (int) round($file->getSize() / 1024),
            'valid'        => false,
            'catatan'      => null,
        ]);

        $this->updateStatusBerkas($pendaftar);

        return $berkas;
    }

    /**
     * Hapus berkas dari storage dan DB.
     */
    public function delete(PendaftarBerkas $berkas, Pendaftar $pendaftar): void
    {
        if (Storage::disk('public')->exists($berkas->url)) {
            Storage::disk('public')->delete($berkas->url);
        }

        $berkas->delete();

        // Update catatan jika berkas tidak cukup
        $total = PendaftarBerkas::where('pendaftar_id', $pendaftar->id)->count();
        if ($total < self::BERKAS_WAJIB_MINIMAL) {
            $pendaftar->update(['catatan_admin' => 'Berkas belum lengkap, upload minimal ' . self::BERKAS_WAJIB_MINIMAL . ' berkas']);
        }
    }

    /**
     * Upload bukti bayar.
     */
    public function uploadBuktiBayar(Pendaftar $pendaftar, UploadedFile $file): PendaftarBerkas
    {
        $fileName = $pendaftar->no_pendaftaran . '_bukti_bayar_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('pembayaran', $fileName, 'public');

        // Hapus bukti bayar lama
        $this->deleteLama($pendaftar->id, 'BUKTI_BAYAR');

        $berkas = PendaftarBerkas::create([
            'pendaftar_id' => $pendaftar->id,
            'jenis'        => 'BUKTI_BAYAR',
            'nama_file'    => $fileName,
            'url'          => $filePath,
            'ukuran_kb'    => (int) round($file->getSize() / 1024),
            'valid'        => false,
            'catatan'      => 'Menunggu validasi pembayaran',
        ]);

        $pendaftar->update([
            'catatan_admin' => 'Bukti pembayaran telah diupload, menunggu validasi keuangan',
        ]);

        return $berkas;
    }

    /**
     * Cek apakah berkas pendaftar sudah lengkap.
     */
    public function sudahLengkap(int $pendaftarId): bool
    {
        return PendaftarBerkas::where('pendaftar_id', $pendaftarId)->count() >= self::BERKAS_WAJIB_MINIMAL;
    }

    /**
     * Hitung sisa berkas yang perlu diupload.
     */
    public function sisaBerkas(int $pendaftarId): int
    {
        $total = PendaftarBerkas::where('pendaftar_id', $pendaftarId)->count();
        return max(0, self::BERKAS_WAJIB_MINIMAL - $total);
    }

    // ─────────────────── Private Helpers ───────────────────

    private function generateFileName(string $noPendaftaran, string $jenisBerkas, UploadedFile $file): string
    {
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return "{$noPendaftaran}_{$jenisBerkas}_{$baseName}.{$file->getClientOriginalExtension()}";
    }

    private function deleteLama(int $pendaftarId, string $jenis): void
    {
        PendaftarBerkas::where('pendaftar_id', $pendaftarId)
            ->where('jenis', $jenis)
            ->delete();
    }

    private function updateStatusBerkas(Pendaftar $pendaftar): void
    {
        $total = PendaftarBerkas::where('pendaftar_id', $pendaftar->id)->count();

        $catatan = $total >= self::BERKAS_WAJIB_MINIMAL
            ? 'Berkas lengkap, menunggu verifikasi administrasi'
            : 'Berkas belum lengkap, upload minimal ' . self::BERKAS_WAJIB_MINIMAL . ' berkas';

        $pendaftar->update(['catatan_admin' => $catatan]);
    }
}
