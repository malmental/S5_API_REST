<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Incidence;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::all();

        return response()->json([
            'data' => $users,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'data' => $user,
        ]);
    }

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
