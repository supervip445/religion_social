<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

            if ($request->filled('type')) {
                $query->where('type', $request->get('type'));
            }
            
            // Get users with their roles
            $perPage = (int) $request->integer('per_page', 100);
            $users = $query->with('roles:id,title')
                ->select('id', 'name', 'age', 'phone', 'email', 'type', 'user_name', 'status', 'created_at')
                ->paginate($perPage);

            $users->getCollection()->transform(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'age' => $user->age,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'user_name' => $user->user_name,
                        'type' => $user->type?->value ?? null,
                        'status' => $user->status,
                        'created_at' => optional($user->created_at)->toIso8601String(),
                        'roles' => $user->roles->pluck('title')->toArray(),
                    ];
                });
            
            return response()->json([
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                    'has_more_pages' => $users->hasMorePages(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('UserController index error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:1|max:150',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8',
            'status' => 'nullable|integer|in:0,1',
        ]);

        do {
            $userName = 'P' . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
        } while (User::where('user_name', $userName)->exists());

        $user = User::create([
            'name' => $validated['name'],
            'age' => $validated['age'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'user_name' => $userName,
            'password' => Hash::make($validated['password']),
            'type' => UserType::User->value,
            'status' => $validated['status'] ?? 1,
            'is_changed_password' => 1,
            'agent_id' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'age' => $user->age,
                'phone' => $user->phone,
                'email' => $user->email,
                'user_name' => $user->user_name,
                'type' => $user->type->value,
                'status' => $user->status,
            ],
        ], 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'age' => $user->age,
                'phone' => $user->phone,
                'email' => $user->email,
                'user_name' => $user->user_name,
                'type' => $user->type?->value ?? null,
                'status' => $user->status,
                'created_at' => optional($user->created_at)->toIso8601String(),
                'updated_at' => optional($user->updated_at)->toIso8601String(),
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:150',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'status' => 'nullable|integer|in:0,1',
        ]);

        $user->name = $validated['name'];
        $user->age = $validated['age'] ?? null;
        $user->phone = $validated['phone'];
        $user->email = $validated['email'] ?? null;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->is_changed_password = 1;
        }
        if (array_key_exists('status', $validated)) {
            $user->status = $validated['status'];
        }
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'age' => $user->age,
                'phone' => $user->phone,
                'email' => $user->email,
                'user_name' => $user->user_name,
                'type' => $user->type?->value ?? null,
                'status' => $user->status,
            ],
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}

