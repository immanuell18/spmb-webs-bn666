<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftarBerkas extends Model
{
    protected $table = 'pendaftar_berkas';
    
    protected $fillable = [
        'pendaftar_id',
        'jenis',
        'nama_file',
        'url',
        'ukuran_kb',
        'valid',
        'catatan'
    ];

    public function pendaftar()
    {
        return $this->belongsTo(Pendaftar::class);
    }
    
    // Accessor untuk compatibility
    public function getJenisBerkasAttribute()
    {
        return strtolower($this->jenis);
    }
    
    public function getPathFileAttribute()
    {
        return $this->url;
    }
    
    public function getStatusAttribute()
    {
        return $this->valid ? 'valid' : 'pending';
    }
}