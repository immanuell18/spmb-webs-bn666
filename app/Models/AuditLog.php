<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    public static function logActivity($data)
    {
        self::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'System',
            'action' => $data['action'],
            'table_name' => $data['table_name'] ?? 'system',
            'record_id' => $data['record_id'] ?? null,
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public static function log($action, $tableName, $recordId = null, $oldValues = null, $newValues = null)
    {
        self::logActivity([
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues
        ]);
    }
}