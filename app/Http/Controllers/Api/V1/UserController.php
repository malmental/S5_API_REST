<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Incidence;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @group Users
 * Endpoints for managing users, including listing all users, viewing a single user, retrieving a user's incidences, and deleting a user.
 */
class UserController extends Controller
{
    /**
     * List all users.
     * Retrieve a list of all users.
     * 
     * @authenticated
     * @response 200 scenario="Users retrieved" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "User Admin",
     *       "email": "admin@telsur.cl",
     *       "is_admin": true,
     *       "created_at": "2026-01-01T00:00:00Z",
     *       "updated_at": "2026-01-01T00:00:00Z"
     *     },
     *     {
     *       "id": 2,
     *       "name": "User no Admin",
     *       "email": "noadmin@telsur.cl",
     *       "is_admin": false,
     *       ...
     *     }
     *   ]
     * }
     * @response 401 scenario="Unauthorized" {
     *   "message": "Unauthenticated."
     * }
     * @response 403 scenario="Forbidden" {
     *   "message": "Forbidden. Admin access required."
     * }
     */
    public function index(): JsonResponse
    {
        $users = User::all();

        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * View a single user.
     * Get detailed information about a specific user by their ID.
     * 
     * @authenticated
     * @urlParam id integer required The ID of the user. Example: 1
     * 
     * @response 200 scenario="User retrieved" {
     *   "data": {
     *     "id": 1,
     *     "name": "User Admin",
     *     "email": "admin@telsur.cl",
     *     "is_admin": true,
     *     "created_at": "2026-01-01T00:00:00Z",
     *     "updated_at": "2026-01-01T00:00:00Z"
     *   }
     * }
     * @response 401 scenario="Unauthorized" {
     *   "message": "Unauthenticated."
     * }
     * @response 403 scenario="Forbidden" {
     *   "message": "Forbidden. Admin access required."
     * }
     * @response 404 scenario="User not found" {
     *   "message": "User not found."
     * }
     */
    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Get user's incidences.
     * Retrieve all incidences created by or assigned to a specific user by their ID.
     * - The creartor of the incidence is determined by the `user_id` field.
     * - The assigned user is determined by the `assigned_to` field.
     * 
     * @authenticated
     * @urlParam id integer required The ID of the user. Example: 2
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Server Down",
     *       "user_id": 2,
     *       "assigned_to": 2,
     *       ...
     *     },
     *     {
     *       "id": 3,
     *       "title": "Database issue",
     *       "user_id": 1,
     *       "assigned_to": 2,
     *       ...
     *     }
     *   ]
     * }
     * @response 401 scenario="Unauthorized" {
     *   "message": "Unauthenticated."
     * }
     * @response 403 scenario="Forbidden" {
     *   "message": "Forbidden. Admin access required."
     * }
     * @response 404 scenario="User not found" {
     *   "message": "No query results for model [App\\Models\\User]."
     * }
     */
    public function incidences(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $incidences = Incidence::where('user_id', $user->id)
            ->orWhere('assigned_to', $user->id)
            ->get();

        return response()->json([
            'data' => $incidences,
        ]);
    }

    /**
     * Delete a user.
     * Remove a specific user from the system.
     * Only an admin can delete a user, and users cannot delete themselves.
     * 
     * @authenticated
     * @urlParam id integer required The ID of the user. Example: 1
     * 
     * @response 200 scenario="User deleted" {
     *   "message": "User deleted successfully."
     * }
     * @response 401 scenario="Unauthorized" {
     *   "message": "Unauthenticated."
     * }
     * @response 403 scenario="Forbidden" {
     *   "message": "Forbidden. Admin access required."
     * }
     * @response 404 scenario="User not found" {
     *   "message": "User not found."}
     */
    public function destroy(int $id): JsonResponse
    {
        if (auth()->id() === $id) {
        return response()->json([
                'message' => 'Cannot delete yourself.',
            ], 403);
        }
    
        $user = User::findOrFail($id);
    
        $user->delete();
    
        return response()->json([
        'message' => 'User deleted successfully.',
        ], 200);
    }
}
