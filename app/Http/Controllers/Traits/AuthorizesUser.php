<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait AuthorizesUser
{
    /**
     * Authorize that the current user is the owner of the resource OR is an admin.
     * Returns 403 if neither condition is met.
     *
     * @param  object  $model  The model to check ownership on
     * @param  string|null  $ownerField  The field name that holds the owner user_id (default: 'user_id')
     * @return JsonResponse|null Returns JsonResponse with 403 if unauthorized, null if authorized
     */
    protected function authorizeOwnerOrAdmin(object $model, ?string $ownerField = 'user_id'): ?JsonResponse
    {
        $user = auth()->user();
        $ownerId = $model->{$ownerField};

        if (! $user->isAdmin() && $ownerId !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return null;
    }

    /**
     * Authorize that the current user is the owner of the resource.
     * Returns 403 if not the owner.
     *
     * @param  object  $model  The model to check ownership on
     * @param  string|null  $ownerField  The field name that holds the owner user_id (default: 'user_id')
     * @return JsonResponse|null Returns JsonResponse with 403 if unauthorized, null if authorized
     */
    protected function authorizeOwner(object $model, ?string $ownerField = 'user_id'): ?JsonResponse
    {
        $ownerId = $model->{$ownerField};

        if ($ownerId !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return null;
    }
}
