<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftar_id',
        'order_id',
        'amount',
        'currency',
        'payment_method',
        'payment_type', // bank_transfer, qris
        'bank_code',
        'bank_name',
        'account_number',
        'qr_code_url',
        'status',
        'gateway',
        'snap_token',
        'payment_data',
        'gateway_response',
        'refund_amount',
        'paid_at',
        'refunded_at',
        'expires_at'
    ];

    protected $casts = [
        'payment_data' => 'array',
        'gateway_response' => 'array',
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function pendaftar()
    {
        return $this->belongsTo(Pendaftar::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function canBeRefunded()
    {
        return $this->status === 'paid' && !$this->refunded_at;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Menunggu</span>',
            'paid' => '<span class="badge bg-success">Lunas</span>',
            'failed' => '<span class="badge bg-danger">Gagal</span>',
            'cancelled' => '<span class="badge bg-secondary">Dibatalkan</span>',
            'refunded' => '<span class="badge bg-info">Dikembalikan</span>',
            'expired' => '<span class="badge bg-dark">Kedaluwarsa</span>',
            default => '<span class="badge bg-light">Unknown</span>'
        };
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getPaymentMethodBadgeAttribute()
    {
        return match($this->payment_type) {
            'bank_transfer' => '<span class="badge bg-primary"><i class="fas fa-university"></i> Transfer Bank</span>',
            'qris' => '<span class="badge bg-info"><i class="fas fa-qrcode"></i> QRIS</span>',
            default => '<span class="badge bg-secondary">Manual</span>'
        };
    }

    public function getBankInfoAttribute()
    {
        if ($this->payment_type === 'bank_transfer') {
            return $this->bank_name . ' - ' . $this->account_number;
        }
        return null;
    }
}