<?php

namespace App\Traits;

use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HasPermissions
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Check if the authenticated user has a specific permission.
     */
    protected function userCan(string $permission): bool
    {
        $user = auth()->user();
        return $user && $this->permissionService->userCan($user, $permission);
    }

    /**
     * Check if the authenticated user has any of the given permissions.
     */
    protected function userCanAny(array $permissions): bool
    {
        $user = auth()->user();
        return $user && $this->permissionService->userCanAny($user, $permissions);
    }

    /**
     * Check if the authenticated user has all of the given permissions.
     */
    protected function userCanAll(array $permissions): bool
    {
        $user = auth()->user();
        return $user && $this->permissionService->userCanAll($user, $permissions);
    }

    /**
     * Check if the authenticated user has a specific role.
     */
    protected function userHasRole(string $role): bool
    {
        $user = auth()->user();
        return $user && $this->permissionService->userHasRole($user, $role);
    }

    /**
     * Check if the authenticated user has any of the given roles.
     */
    protected function userHasAnyRole(array $roles): bool
    {
        $user = auth()->user();
        return $user && $this->permissionService->userHasAnyRole($user, $roles);
    }

    /**
     * Return a forbidden response if user doesn't have permission.
     */
    protected function requirePermission(string $permission): ?JsonResponse
    {
        if (!$this->userCan($permission)) {
            return response()->json([
                'message' => 'Access denied. Insufficient permissions.',
                'required_permission' => $permission
            ], 403);
        }

        return null;
    }

    /**
     * Return a forbidden response if user doesn't have any of the given permissions.
     */
    protected function requireAnyPermission(array $permissions): ?JsonResponse
    {
        if (!$this->userCanAny($permissions)) {
            return response()->json([
                'message' => 'Access denied. Insufficient permissions.',
                'required_permissions' => $permissions
            ], 403);
        }

        return null;
    }

    /**
     * Return a forbidden response if user doesn't have all of the given permissions.
     */
    protected function requireAllPermissions(array $permissions): ?JsonResponse
    {
        if (!$this->userCanAll($permissions)) {
            return response()->json([
                'message' => 'Access denied. Insufficient permissions.',
                'required_permissions' => $permissions
            ], 403);
        }

        return null;
    }

    /**
     * Return a forbidden response if user doesn't have the given role.
     */
    protected function requireRole(string $role): ?JsonResponse
    {
        if (!$this->userHasRole($role)) {
            return response()->json([
                'message' => 'Access denied. Insufficient role privileges.',
                'required_role' => $role
            ], 403);
        }

        return null;
    }

    /**
     * Return a forbidden response if user doesn't have any of the given roles.
     */
    protected function requireAnyRole(array $roles): ?JsonResponse
    {
        if (!$this->userHasAnyRole($roles)) {
            return response()->json([
                'message' => 'Access denied. Insufficient role privileges.',
                'required_roles' => $roles
            ], 403);
        }

        return null;
    }

    /**
     * Check if user is a super admin.
     */
    protected function isSuperAdmin(): bool
    {
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    /**
     * Check if user is an admin.
     */
    protected function isAdmin(): bool
    {
        $user = auth()->user();
        return $user && $user->isAdmin();
    }

    /**
     * Check if user is a manager.
     */
    protected function isManager(): bool
    {
        $user = auth()->user();
        return $user && $user->isManager();
    }

    /**
     * Check if user is a loan officer.
     */
    protected function isLoanOfficer(): bool
    {
        $user = auth()->user();
        return $user && $user->isLoanOfficer();
    }

    /**
     * Check if user is a cashier.
     */
    protected function isCashier(): bool
    {
        $user = auth()->user();
        return $user && $user->isCashier();
    }

    /**
     * Check if user is an accountant.
     */
    protected function isAccountant(): bool
    {
        $user = auth()->user();
        return $user && $user->isAccountant();
    }

    /**
     * Check if user is a customer service representative.
     */
    protected function isCustomerService(): bool
    {
        $user = auth()->user();
        return $user && $user->isCustomerService();
    }

    /**
     * Check if user is an owner.
     */
    protected function isOwner(): bool
    {
        $user = auth()->user();
        return $user && $user->isOwner();
    }

    /**
     * Check if user is a borrower.
     */
    protected function isBorrower(): bool
    {
        $user = auth()->user();
        return $user && $user->isBorrower();
    }

    /**
     * Check if user has administrative privileges.
     */
    protected function hasAdminPrivileges(): bool
    {
        $user = auth()->user();
        return $user && $user->hasAdminPrivileges();
    }

    /**
     * Check if user has financial privileges.
     */
    protected function hasFinancialPrivileges(): bool
    {
        $user = auth()->user();
        return $user && $user->hasFinancialPrivileges();
    }

    /**
     * Check if user has loan management privileges.
     */
    protected function hasLoanPrivileges(): bool
    {
        $user = auth()->user();
        return $user && $user->hasLoanPrivileges();
    }

    /**
     * Check if user has customer service privileges.
     */
    protected function hasCustomerServicePrivileges(): bool
    {
        $user = auth()->user();
        return $user && $user->hasCustomerServicePrivileges();
    }
}
