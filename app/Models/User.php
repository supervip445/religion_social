<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWalletFloat;
use App\Models\Admin\Role;
use App\Models\Admin\Permission;

class User extends Authenticatable implements Wallet
{
    use HasApiTokens, HasFactory, Notifiable, HasWalletFloat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'name',
        'age',
        'profile',
        'email',
        'password',
        'profile',
        'phone',
        'status',
        'type',
        'is_changed_password',
        'user_agent',
        'teacher_id',
        'agent_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'type' => UserType::class,
    ];

    /**
     * Get the roles that belong to this user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the permissions that belong to this user directly.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    // academic year
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('title', $role);
        }
        
        return $this->roles->contains($role);
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->whereIn('title', $roles)->isNotEmpty();
    }

    /**
     * Check if user has all of the given roles.
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles->whereIn('title', $roles)->count() === count($roles);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permission): bool
    {
        // Check direct permissions first
        if (is_string($permission)) {
            $hasDirectPermission = $this->permissions->contains('title', $permission);
            if ($hasDirectPermission) {
                return true;
            }
        } else {
            $hasDirectPermission = $this->permissions->contains($permission);
            if ($hasDirectPermission) {
                return true;
            }
        }

        // Check permissions through roles
        return $this->roles->flatMap->permissions->pluck('title')->contains($permission);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        // Check direct permissions first
        $userDirectPermissions = $this->permissions->pluck('title')->toArray();
        $hasDirectPermission = !empty(array_intersect($userDirectPermissions, $permissions));
        if ($hasDirectPermission) {
            return true;
        }

        // Check permissions through roles
        $rolePermissions = $this->roles->flatMap->permissions->pluck('title')->toArray();
        return !empty(array_intersect($rolePermissions, $permissions));
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        // Get all user permissions (direct + through roles)
        $userDirectPermissions = $this->permissions->pluck('title')->toArray();
        $rolePermissions = $this->roles->flatMap->permissions->pluck('title')->toArray();
        $allUserPermissions = array_unique(array_merge($userDirectPermissions, $rolePermissions));

        return count(array_intersect($allUserPermissions, $permissions)) === count($permissions);
    }

    /**
     * Get all permissions for the user (direct + through roles).
     */
    public function getAllPermissions(): \Illuminate\Support\Collection
    {
        $directPermissions = $this->permissions;
        $rolePermissions = $this->roles->flatMap->permissions;
        
        return $directPermissions->merge($rolePermissions)->unique('id');
    }

    /**
     * Get all permission titles for the user.
     */
    public function getAllPermissionTitles(): array
    {
        return $this->getAllPermissions()->pluck('title')->unique()->toArray();
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole($role): void
    {
        if (is_string($role)) {
            $roleModel = Role::where('title', $role)->first();
            if ($roleModel && !$this->hasRole($roleModel)) {
                $this->roles()->attach($roleModel->id);
            }
        } else {
            if (!$this->hasRole($role)) {
                $this->roles()->attach($role->id);
            }
        }
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole($role): void
    {
        if (is_string($role)) {
            $roleModel = Role::where('title', $role)->first();
            if ($roleModel) {
                $this->roles()->detach($roleModel->id);
            }
        } else {
            $this->roles()->detach($role->id);
        }
    }

    /**
     * Sync roles for user (replace all existing roles).
     */
    public function syncRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
    }

    /**
     * Assign a permission directly to the user.
     */
    public function assignPermission($permission): void
    {
        if (is_string($permission)) {
            $permissionModel = Permission::where('title', $permission)->first();
            if ($permissionModel && !$this->permissions->contains($permissionModel)) {
                $this->permissions()->attach($permissionModel->id);
            }
        } else {
            if (!$this->permissions->contains($permission)) {
                $this->permissions()->attach($permission->id);
            }
        }
    }

    /**
     * Remove a permission directly from the user.
     */
    public function removePermission($permission): void
    {
        if (is_string($permission)) {
            $permissionModel = Permission::where('title', $permission)->first();
            if ($permissionModel) {
                $this->permissions()->detach($permissionModel->id);
            }
        } else {
            $this->permissions()->detach($permission->id);
        }
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin') || $this->isSuperAdmin();
    }

    /**
     * Check if user is a manager.
     */
    public function isManager(): bool
    {
        return $this->hasRole('Manager') || $this->isAdmin();
    }

    /**
     * Check if user is a loan officer.
     */
    public function isLoanOfficer(): bool
    {
        return $this->hasRole('Loan Officer') || $this->isManager();
    }

    /**
     * Check if user is a cashier.
     */
    public function isCashier(): bool
    {
        return $this->hasRole('Cashier') || $this->isManager();
    }

    /**
     * Check if user is an accountant.
     */
    public function isAccountant(): bool
    {
        return $this->hasRole('Accountant') || $this->isManager();
    }

    /**
     * Check if user is a customer service representative.
     */
    public function isCustomerService(): bool
    {
        return $this->hasRole('Customer Service') || $this->isManager();
    }

    /**
     * Check if user is an owner.
     */
    public function isOwner(): bool
    {
        return $this->hasRole('Owner') || $this->isAdmin();
    }

    /**
     * Check if user is a borrower.
     */
    public function isBorrower(): bool
    {
        return $this->hasRole('Borrower');
    }

    /**
     * Get the user type as UserType enum.
     */
    public function getUserType(): UserType
    {
        return $this->type;
    }

    /**
     * Check if user is of a specific type.
     */
    public function isType(UserType $type): bool
    {
        return $this->type === $type;
    }

    /**
     * Get the display name for the user type.
     */
    public function getTypeDisplayName(): string
    {
        return $this->type->getDisplayName();
    }

    /**
     * Get the role name for the user type.
     */
    public function getRoleName(): string
    {
        return $this->type->getRoleName();
    }

    /**
     * Check if user has administrative privileges.
     */
    public function hasAdminPrivileges(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isManager();
    }

    /**
     * Check if user has financial privileges.
     */
    public function hasFinancialPrivileges(): bool
    {
        return $this->isAccountant() || $this->isCashier() || $this->hasAdminPrivileges();
    }

    /**
     * Check if user has loan management privileges.
     */
    public function hasLoanPrivileges(): bool
    {
        return $this->isLoanOfficer() || $this->hasAdminPrivileges();
    }

    /**
     * Check if user has customer service privileges.
     */
    public function hasCustomerServicePrivileges(): bool
    {
        return $this->isCustomerService() || $this->hasAdminPrivileges();
    }
}
