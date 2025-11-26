<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';
    
    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'kuota'
    ];

    public function pendaftar()
    {
        return $this->hasMany(Pendaftar::class);
    }

    public function getJumlahPendaftarAttribute()
    {
        return $this->pendaftar()->whereIn('status', ['SUBMIT', 'ADM_PASS', 'PAID'])->count();
    }

    public function getSisaKuotaAttribute()
    {
        return max(0, $this->kuota - $this->jumlah_pendaftar);
    }

    public function getIsKuotaPenuhAttribute()
    {
        return $this->sisa_kuota <= 0;
    }

    public function canRegister()
    {
        $jumlahPendaftar = $this->pendaftar()->whereIn('status', ['SUBMIT', 'ADM_PASS', 'PAID'])->count();
        return $jumlahPendaftar < $this->kuota;
    }
    
    public function getPendaftarCountAttribute()
    {
        return $this->pendaftar()->whereIn('status', ['SUBMIT', 'ADM_PASS', 'PAID'])->count();
    }
}