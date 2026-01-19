<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PublicAuthController extends Controller
{
    /**
     * Register a new public user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:150',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Generate unique username from phone number
        $userName = 'user_' . $request->phone;
        $counter = 1;
        while (User::where('user_name', $userName)->exists()) {
            $userName = 'user_' . $request->phone . '_' . $counter;
            $counter++;
        }

        // Get the first SuperAdmin to assign as agent/admin for chat system
        $admin = User::where('type', UserType::SuperAdmin->value)
            ->where('status', 1)
            ->first();

        // Create user
        $user = User::create([
            'name' => $request->name,
            'age' => $request->age,
            'phone' => $request->phone,
            'user_name' => $userName,
            'password' => Hash::make($request->password),
            'type' => UserType::User->value,
            'status' => 1,
            'is_changed_password' => 1,
            'agent_id' => $admin?->id, // Assign admin/agent for chat system
        ]);

        // Auto-login: Create token
        $token = $user->createToken('public-user-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'age' => $user->age,
                'phone' => $user->phone,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'type' => $user->type->value,
            ],
        ], 201);
    }

    /**
     * Login for public users
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)
            ->where('type', UserType::User->value)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user is active
        if ($user->status !== 1) {
            return response()->json([
                'message' => 'Your account is inactive. Please contact administrator.',
            ], 403);
        }

        $token = $user->createToken('public-user-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'age' => $user->age,
                'phone' => $user->phone,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'type' => $user->type->value,
            ],
        ]);
    }

    /**
     * Get current public user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if ($user->type !== UserType::User) {
            return response()->json([
                'message' => 'Access denied.',
            ], 403);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'age' => $user->age,
                'phone' => $user->phone,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'type' => $user->type->value,
                'profile' => $user->profile,
            ],
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}

