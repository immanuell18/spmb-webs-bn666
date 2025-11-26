<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftar extends Model
{
    use HasFactory;

    protected $table = 'pendaftar';
    
    protected $fillable = [
        'user_id',
        'no_pendaftaran',
        'nama',
        'email',
        'jurusan_id',
        'gelombang_id',
        'wilayah_id',
        'tanggal_daftar',
        'status',
        'status_akhir',
        'status_pembayaran',
        'biaya_pendaftaran',
        'tgl_pengumuman',
        'user_pengumuman',
        'catatan_admin',
        'tanggal_verifikasi',
        'tanggal_pembayaran',
        'tanggal_kelulusan',
        'user_verifikasi_adm',
        'tgl_verifikasi_adm',
        'user_verifikasi_payment',
        'tgl_verifikasi_payment'
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
        'tgl_verifikasi_adm' => 'datetime',
        'tgl_verifikasi_payment' => 'datetime',
        'tgl_pengumuman' => 'datetime',
        'biaya_pendaftaran' => 'integer',
    ];

    // Status constants sesuai UKK
    const STATUS_SUBMIT = 'SUBMIT';           // Form terkirim, menunggu verifikasi administrasi
    const STATUS_ADM_PASS = 'ADM_PASS';       // Lulus administrasi, boleh bayar
    const STATUS_ADM_REJECT = 'ADM_REJECT';   // Tolak administrasi, perlu perbaikan berkas
    const STATUS_PAID = 'PAID';               // Sudah bayar, menunggu pengumuman

    const STATUS_AKHIR_LULUS = 'LULUS';
    const STATUS_AKHIR_TIDAK_LULUS = 'TIDAK_LULUS';
    const STATUS_AKHIR_CADANGAN = 'CADANGAN';

    // Status flow helper methods
    public function canProceedToPayment()
    {
        return $this->status === self::STATUS_ADM_PASS;
    }

    public function canBeAnnounced()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function getStatusLabel()
    {
        // Jika sudah ada status akhir, tampilkan status akhir
        if ($this->status_akhir) {
            $statusAkhir = [
                self::STATUS_AKHIR_LULUS => 'LULUS',
                self::STATUS_AKHIR_TIDAK_LULUS => 'TIDAK LULUS',
                self::STATUS_AKHIR_CADANGAN => 'CADANGAN',
            ];
            return $statusAkhir[$this->status_akhir] ?? $this->status_akhir;
        }
        
        // Jika belum ada status akhir, tampilkan status proses
        $labels = [
            self::STATUS_SUBMIT => 'Menunggu Verifikasi Administrasi',
            self::STATUS_ADM_PASS => 'Lulus Administrasi - Silakan Bayar',
            self::STATUS_ADM_REJECT => 'Berkas Ditolak - Perlu Perbaikan',
            self::STATUS_PAID => 'Sudah Bayar - Menunggu Pengumuman',
        ];
        return $labels[$this->status] ?? 'Status Tidak Dikenal';
    }

    public function getProgressPercentage()
    {
        // Jika sudah ada status akhir, progress 100%
        if ($this->status_akhir) {
            return 100;
        }
        
        $progress = [
            self::STATUS_SUBMIT => 25,
            self::STATUS_ADM_PASS => 50,
            self::STATUS_ADM_REJECT => 25, // Kembali ke awal
            self::STATUS_PAID => 75,
        ];
        
        return $progress[$this->status] ?? 0;
    }

    public function getBerkasStatus()
    {
        $berkasCount = $this->berkas()->count();
        $requiredBerkas = 4; // foto, ijazah, rapor, kk
        
        if ($berkasCount >= $requiredBerkas) {
            return 'Lengkap';
        } elseif ($berkasCount > 0) {
            return "Belum Lengkap ({$berkasCount}/{$requiredBerkas})";
        } else {
            return 'Belum Upload';
        }
    }

    public function getStatusBadgeColor()
    {
        // Jika sudah ada status akhir
        if ($this->status_akhir) {
            $colors = [
                self::STATUS_AKHIR_LULUS => 'success',
                self::STATUS_AKHIR_TIDAK_LULUS => 'danger',
                self::STATUS_AKHIR_CADANGAN => 'warning',
            ];
            return $colors[$this->status_akhir] ?? 'secondary';
        }
        
        // Jika belum ada status akhir
        $colors = [
            self::STATUS_SUBMIT => 'info',
            self::STATUS_ADM_PASS => 'primary',
            self::STATUS_ADM_REJECT => 'danger',
            self::STATUS_PAID => 'warning',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function gelombang()
    {
        return $this->belongsTo(Gelombang::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function dataSiswa()
    {
        return $this->hasOne(PendaftarDataSiswa::class);
    }

    public function dataOrtu()
    {
        return $this->hasOne(PendaftarDataOrtu::class);
    }

    public function asalSekolah()
    {
        return $this->hasOne(PendaftarAsalSekolah::class);
    }

    public function berkas()
    {
        return $this->hasMany(PendaftarBerkas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function latestPaymentTransaction()
    {
        return $this->hasOne(PaymentTransaction::class)->latest();
    }
}