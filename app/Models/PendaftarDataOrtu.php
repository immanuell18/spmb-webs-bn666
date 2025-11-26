<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftarDataOrtu extends Model
{
    protected $table = 'pendaftar_data_ortu';
    
    protected $fillable = [
        'pendaftar_id',
        'nama_ayah',
        'pekerjaan_ayah',
        'hp_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'hp_ibu',
        'wali_nama',
        'wali_hp'
    ];

    public function pendaftar()
    {
        return $this->belongsTo(Pendaftar::class);
    }
}