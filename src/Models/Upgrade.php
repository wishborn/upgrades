<?php

namespace Wishborn\Upgrades\Models;

use Illuminate\Database\Eloquent\Model;

class Upgrade extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wishborn_upgrades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'batch',
        'executed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'executed_at' => 'datetime',
    ];
} 