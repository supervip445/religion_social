<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonasteryBuildingDonation extends Model
{
    use HasFactory;

    protected $table = 'monastery_building_donations';

    protected $fillable = [
        'donor_name',
        'amount',
        'donation_purpose',
        'date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];
}

