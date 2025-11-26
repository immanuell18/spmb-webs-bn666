<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';
    protected $fillable = ['id', 'regency_id', 'name'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }

    public function villages()
    {
        return $this->hasMany(\App\Models\Village::class, 'district_id');
    }
}