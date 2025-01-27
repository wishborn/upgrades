<?php

namespace Wishborn\Upgrades\Models;

use Illuminate\Database\Eloquent\Model;

class Upgrade extends Model
{
    protected $fillable = [
        'name',
        'batch',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];
} 