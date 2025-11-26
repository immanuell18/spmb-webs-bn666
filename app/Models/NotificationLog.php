<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'pendaftar_id',
        'type',
        'status',
        'data',
        'error_message',
        'sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime'
    ];

    public function pendaftar()
    {
        return $this->belongsTo(Pendaftar::class);
    }
}