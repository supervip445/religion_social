<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users (for use as teachers)
     * Since there's no separate teacher role, we use admin users as teachers
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();
            
            // Filter by role if provided (checking roles relationship)
            if ($request->has('role')) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('title', $request->role);
                });
            }
            
            // Get users with their roles
            $users = $query->with('roles:id,title')
                ->select('id', 'name', 'email', 'type', 'user_name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'user_name' => $user->user_name,
                        'type' => $user->type?->value ?? null,
                        'roles' => $user->roles->pluck('title')->toArray(),
                    ];
                });
            
            return response()->json(['data' => $users]);
        } catch (\Exception $e) {
            \Log::error('UserController index error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

