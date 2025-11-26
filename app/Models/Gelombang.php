<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Gelombang extends Model
{
    use HasFactory;

    protected $table = 'gelombang';
    
    protected $fillable = [
        'nama',
        'tahun',
        'tgl_mulai',
        'tgl_selesai',
        'biaya_daftar',
        'status'
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'biaya_daftar' => 'decimal:2'
    ];

    public function pendaftar()
    {
        return $this->hasMany(Pendaftar::class);
    }

    // Cek apakah gelombang sedang aktif
    public function isActive()
    {
        $now = Carbon::now();
        return $this->status === 'aktif' && 
               $now->between($this->tgl_mulai, $this->tgl_selesai);
    }

    // Get gelombang yang sedang aktif
    public static function getActive()
    {
        $now = Carbon::now();
        return self::where('status', 'aktif')
                  ->where('tgl_mulai', '<=', $now)
                  ->where('tgl_selesai', '>=', $now)
                  ->first();
    }

    // Get semua gelombang yang tersedia untuk pendaftaran
    public static function getAvailable()
    {
        $now = Carbon::now();
        return self::where('status', 'aktif')
                  ->where('tgl_mulai', '<=', $now)
                  ->where('tgl_selesai', '>=', $now)
                  ->get();
    }
}