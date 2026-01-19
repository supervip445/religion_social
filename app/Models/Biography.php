<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Biography extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'birth_year',
        'sangha_entry_year',
        'disciples',
        'teaching_monastery',
        'sangha_dhamma',
        'image',
    ];

    /**
     * Get all likes for the biography.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Get all comments for the biography.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get approved comments for the biography.
     */
    public function approvedComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->where('is_approved', true);
    }

    /**
     * Get all views for the biography.
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

