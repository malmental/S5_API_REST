<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 * Endpoints for user registration, login, logout and profile view.
 */
class AuthController extends Controller
{
    /**
     * Register a new user.
     * Create a new user account and returns an OAuth token.
     *
     * @unauthenticated
     *
     * @bodyParam name string required User's full name (max 255 chars). Example: New User
     * @bodyParam email string required User's email address (must be unique). Example: newuser@example.com
     * @bodyParam password string required Minimum 8 characers. Example: password123
     * @bodyParam password_confirmation string required Must match the password field. Example: password123
     *
     * @response 201 scenario="User created successfully" {
     *   "data": {
     *     "id": 1,
     *     "name": "New User", ",
     *     "email": "newuser@example.com"
     *   },
     *   "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
     * }
     * @response 401 scenario="Unauthenticated" {
     *  "message": "Unauthenticated."
     * }
     * @response 422 scenario="Validation error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login user.
     * Authenticate user with email and password, returns an OAuth token on success.
     *
     * @unauthenticated
     *
     * @bodyParam email string required User's email address.
     * @bodyParam password string required User's password.
     *
     * @response 401 scenario="Invalid credentials" {
     *   "message": "Invalid credentials"
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'token' => $token,
        ], 200);
    }

    /**
     * Logout user.
     * Invalidate the user's current access token, effectively logging them out.
     *
     * @authenticated
     *
     * @response 200 scenario="Log out succesful" {
     *  "message": "Logged out successfully"
     * }
     * @response 401 scenario="Unauthenticated" {
     *  "message": "Unauthenticated."
     * }
     */
    public function logout(): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Get current user profile.
     * Returns the authenticated user's profile information.
     *
     * @authenticated
     *
     * @response 200 scenario="User profile retrieved successfully" {
     *   "data": {
     *     "id": 1,
     *     "name": "New User",
     *     "email": "newuser@example.com"
     *   }
     * }
     * @response 401 scenario="Unauthenticated" {
     *  "message": "Unauthenticated."
     * }
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
            ],
        ], 200);
    }
}
