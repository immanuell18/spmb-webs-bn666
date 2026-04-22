<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PendaftarResource
 *
 * Format JSON konsisten untuk API endpoint pendaftar.
 * Kalau nama kolom DB berubah, cukup update di sini — frontend tidak perlu diubah.
 */
class PendaftarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'no_pendaftaran'   => $this->no_pendaftaran,
            'nama'             => $this->nama,
            'email'            => $this->email,
            'status'           => $this->status,
            'status_label'     => $this->getStatusLabel(),
            'status_warna'     => $this->getStatusBadgeColor(),
            'progress'         => $this->getProgressPercentage(),
            'status_akhir'     => $this->status_akhir,
            'tanggal_daftar'   => $this->tanggal_daftar?->format('Y-m-d H:i'),
            'biaya_pendaftaran' => $this->biaya_pendaftaran,
            'jurusan' => $this->whenLoaded('jurusan', fn() => [
                'id'   => $this->jurusan->id,
                'nama' => $this->jurusan->nama,
                'kode' => $this->jurusan->kode,
            ]),
            'gelombang' => $this->whenLoaded('gelombang', fn() => [
                'id'   => $this->gelombang->id,
                'nama' => $this->gelombang->nama,
            ]),
            'data_siswa' => $this->whenLoaded('dataSiswa', fn() => [
                'nama'      => $this->dataSiswa->nama,
                'nik'       => $this->dataSiswa->nik,
                'jk'        => $this->dataSiswa->jk,
                'agama'     => $this->dataSiswa->agama,
                'tgl_lahir' => $this->dataSiswa->tgl_lahir?->format('Y-m-d'),
                'alamat'    => $this->dataSiswa->alamat,
            ]),
            'berkas_count' => $this->whenLoaded('berkas', fn() => $this->berkas->count()),
            'created_at'   => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
