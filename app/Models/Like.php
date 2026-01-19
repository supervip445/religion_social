<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'likeable_id',
        'likeable_type',
        'user_identifier',
        'type',
    ];

    /**
     * Get the parent likeable model (post, dhamma, etc.).
     */
    public function likeable()
    {
        return $this->morphTo();
    }
}

