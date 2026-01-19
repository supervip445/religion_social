<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_name',
        'amount',
        'donation_type',
        'date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'status' => 'string',
    ];

    /**
     * Get all views for the donation.
     */
    public function views(): MorphMany
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /**
     * Get the total view count.
     */
    public function getViewsCountAttribute(): int
    {
        return $this->views()->count();
    }
}

