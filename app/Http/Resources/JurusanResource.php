<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JurusanResource — Format JSON konsisten untuk data jurusan.
 */
class JurusanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'kode'            => $this->kode,
            'nama'            => $this->nama,
            'deskripsi'       => $this->deskripsi,
            'kuota'           => $this->kuota,
            'jumlah_pendaftar' => $this->when(
                isset($this->pendaftar_count),
                fn() => $this->pendaftar_count
            ),
            'sisa_kuota'      => $this->when(
                isset($this->pendaftar_count),
                fn() => max(0, $this->kuota - $this->pendaftar_count)
            ),
            'persentase_terisi' => $this->when(
                isset($this->pendaftar_count) && $this->kuota > 0,
                fn() => round(($this->pendaftar_count / $this->kuota) * 100, 1)
            ),
        ];
    }
}
