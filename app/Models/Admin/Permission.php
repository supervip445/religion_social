<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    public $table = 'permissions';

    protected $fillable = [
        'title',
        'description',
        'module',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the roles that belong to this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the users that have this permission through their roles.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class);
    }

    /**
     * Check if a permission exists by title.
     */
    public static function existsByTitle(string $title): bool
    {
        return static::where('title', $title)->exists();
    }

    /**
     * Get permission by title.
     */
    public static function findByTitle(string $title): ?self
    {
        return static::where('title', $title)->first();
    }

    /**
     * Get permissions by module.
     */
    public static function getByModule(string $module): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('module', $module)->get();
    }

    /**
     * Get all available modules.
     */
    public static function getModules(): array
    {
        return static::distinct()->pluck('module')->filter()->toArray();
    }

    /**
     * Scope to filter by module.
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by permission type (view, create, edit, delete, etc.).
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('title', 'like', "%_{$type}");
    }
}
