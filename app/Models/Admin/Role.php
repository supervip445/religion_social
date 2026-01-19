<?php

namespace App\Models\Admin;

use App\Models\Admin\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    public $table = 'roles';

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the permissions that belong to this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Check if a role exists by title.
     */
    public static function existsByTitle(string $title): bool
    {
        return static::where('title', $title)->exists();
    }

    /**
     * Get role by title.
     */
    public static function findByTitle(string $title): ?self
    {
        return static::where('title', $title)->first();
    }

    /**
     * Get active roles only.
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)->get();
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permissionTitle): bool
    {
        return $this->permissions()->where('title', $permissionTitle)->exists();
    }

    /**
     * Check if role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionTitles): bool
    {
        return $this->permissions()->whereIn('title', $permissionTitles)->exists();
    }

    /**
     * Check if role has all of the given permissions.
     */
    public function hasAllPermissions(array $permissionTitles): bool
    {
        $rolePermissionCount = $this->permissions()->whereIn('title', $permissionTitles)->count();
        return $rolePermissionCount === count($permissionTitles);
    }

    /**
     * Assign permissions to role.
     */
    public function assignPermissions(array $permissionIds): void
    {
        $this->permissions()->attach($permissionIds);
    }

    /**
     * Remove permissions from role.
     */
    public function removePermissions(array $permissionIds): void
    {
        $this->permissions()->detach($permissionIds);
    }

    /**
     * Sync permissions for role (replace all existing permissions).
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Scope to filter active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter inactive roles.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
