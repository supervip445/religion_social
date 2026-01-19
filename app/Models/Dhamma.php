<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Dhamma extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'speaker',
        'date',
        'image',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get all likes for the dhamma talk.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Get all comments for the dhamma talk.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get approved comments for the dhamma talk.
     */
    public function approvedComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->where('is_approved', true);
    }

    /**
     * Get all views for the dhamma talk.
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

