<?php

namespace App\Services;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class PermissionService
{
    /**
     * Get all permissions grouped by module.
     */
    public function getPermissionsByModule(): array
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $module = $permission->module ?? 'General';
            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    /**
     * Get all active roles.
     */
    public function getActiveRoles(): Collection
    {
        return Role::active()->get();
    }

    /**
     * Get permissions for a specific role.
     */
    public function getRolePermissions(Role $role): Collection
    {
        return $role->permissions;
    }

    /**
     * Get all permissions for a user (direct + through roles).
     */
    public function getUserPermissions(User $user): Collection
    {
        return $user->getAllPermissions();
    }

    /**
     * Assign permissions to a role.
     */
    public function assignPermissionsToRole(Role $role, array $permissionIds): void
    {
        $role->syncPermissions($permissionIds);
    }

    /**
     * Assign roles to a user.
     */
    public function assignRolesToUser(User $user, array $roleIds): void
    {
        $user->syncRoles($roleIds);
    }

    /**
     * Check if user has permission for a specific action.
     */
    public function userCan(User $user, string $permission): bool
    {
        return $user->hasPermission($permission);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function userCanAny(User $user, array $permissions): bool
    {
        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function userCanAll(User $user, array $permissions): bool
    {
        return $user->hasAllPermissions($permissions);
    }

    /**
     * Check if user has a specific role.
     */
    public function userHasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    /**
     * Check if user has any of the given roles.
     */
    public function userHasAnyRole(User $user, array $roles): bool
    {
        return $user->hasAnyRole($roles);
    }

    /**
     * Get users by role.
     */
    public function getUsersByRole(string $roleTitle): Collection
    {
        $role = Role::where('title', $roleTitle)->first();
        return $role ? $role->users : collect();
    }

    /**
     * Get users by permission.
     */
    public function getUsersByPermission(string $permissionTitle): Collection
    {
        $permission = Permission::where('title', $permissionTitle)->first();
        return $permission ? $permission->users : collect();
    }

    /**
     * Create a new permission.
     */
    public function createPermission(array $data): Permission
    {
        return Permission::create($data);
    }

    /**
     * Create a new role.
     */
    public function createRole(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update a permission.
     */
    public function updatePermission(Permission $permission, array $data): bool
    {
        return $permission->update($data);
    }

    /**
     * Update a role.
     */
    public function updateRole(Role $role, array $data): bool
    {
        return $role->update($data);
    }

    /**
     * Delete a permission.
     */
    public function deletePermission(Permission $permission): bool
    {
        return $permission->delete();
    }

    /**
     * Delete a role.
     */
    public function deleteRole(Role $role): bool
    {
        return $role->delete();
    }

    /**
     * Get permission statistics.
     */
    public function getPermissionStats(): array
    {
        return [
            'total_permissions' => Permission::count(),
            'total_roles' => Role::count(),
            'active_roles' => Role::active()->count(),
            'modules' => Permission::getModules(),
        ];
    }

    /**
     * Get role statistics.
     */
    public function getRoleStats(): array
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        
        return [
            'roles' => $roles,
            'total_users_with_roles' => User::whereHas('roles')->count(),
        ];
    }
}
