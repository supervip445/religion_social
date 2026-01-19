<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class View extends Model
{
    protected $fillable = [
        'viewable_type',
        'viewable_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the parent viewable model (Post, Dhamma, Biography, Lesson, Donation).
     */
    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }
}

