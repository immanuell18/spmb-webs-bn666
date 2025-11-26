<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftarDataSiswa extends Model
{
    protected $table = 'pendaftar_data_siswa';
    protected $primaryKey = 'pendaftar_id';
    public $incrementing = false;
    
    protected $fillable = [
        'pendaftar_id',
        'nik',
        'nisn',
        'nama',
        'jk',
        'agama',
        'tmp_lahir',
        'tgl_lahir',
        'alamat',
        'wilayah_id',
        'lat',
        'lng'
    ];

    public function pendaftar()
    {
        return $this->belongsTo(Pendaftar::class);
    }
}