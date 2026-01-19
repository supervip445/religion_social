<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monastery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'monastery_name',
        'monks',
        'novices',
        'total',
        'order',
    ];

    protected $casts = [
        'monks' => 'integer',
        'novices' => 'integer',
        'total' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Calculate total automatically
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($monastery) {
            $monastery->total = $monastery->monks + $monastery->novices;
        });
    }
}

