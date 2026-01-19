<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'commentable_id',
        'commentable_type',
        'name',
        'email',
        'comment',
        'user_identifier',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Get the parent commentable model (post, dhamma, etc.).
     */
    public function commentable()
    {
        return $this->morphTo();
    }
}

