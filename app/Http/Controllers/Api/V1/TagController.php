<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AuthorizesUser;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

/**
 * @group Tags
 * Endpoints for managing tags, including listing, creating, viewing, updating, and deleting tags.
 */
class TagController extends Controller
{
    use AuthorizesUser;

    /**
     * List all tags.
     * Retrieve all tags with their associated users and incidences.
     *
     * @unauthenticated
     *
     * @response 200 scenario="Tags retrieved" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "tag1",
     *       "user_id": 1,
     *       "created_at": "2026-01-01T00:00:00Z",
     *       "updated_at": "2026-01-01T00:00:00Z",
     *       "user": {...},
     *       "incidences": [...]
     *     }
     *   ]
     * }
     */
    public function index(): JsonResponse
    {
        $tags = Tag::with(['user', 'incidences'])->get();

        return response()->json([
            'data' => TagResource::collection($tags),
        ]);
    }

    /**
     * Create a new tag.
     * Create a new tag or reuse an existing one with the same name (case-insensitive).
     * Uses firstOrCreate to ensure atomicity and prevent duplicates.
     * Tag names are stored in lowercase to enforce case-insensitivity.
     *
     * @authenticated
     *
     * @bodyParam name string required The name of the tag (stored in lowercase). Example: Server
     *
     * @response 201 scenario="Tag created" {
     *   "data": {
     *     "id": 1,
     *     "name": "server",
     *     "user_id": 1,
     *     "created_at": "2026-01-01T00:00:00Z",
     *     "updated_at": "2026-01-01T00:00:00Z",
     *     "user": {...},
     *     "incidences": [...]
     *   }
     * }
     * @response 401 scenario="Unauthorized" {
     *   "message": "Unauthenticated."
     * }
     * @response 422 scenario="Validation error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "name": ["The name field is required."]
     *   }
     * }
     */
    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = Tag::firstOrCreate(
            ['name' => strtolower($request->name)],
            ['user_id' => auth()->id()]
        );

        return response()->json([
            'data' => new TagResource($tag->load(['user', 'incidences'])),
        ], 201);
    }

    /**
     * View a single tag.
     * Get detailed information about a specific tag by its ID, including the user who created it and the incidences associated with it.
     *
     * @unauthenticated
     *
     * @urlParam id integer required The ID of the tag. Example: 1
     *
     * @response 200 scenario="Tag retrieved" {
     *   "data": {
     *     "id": 1,
     *     "name": "server",
     *     "user_id": 1,
     *     "created_at": "2026-01-01T00:00:00Z",
     *     "updated_at": "2026-01-01T00:00:00Z",
     *     "user": {...},
     *     "incidences": [...]
     *   }
     * }
     */
    public function show(Tag $tag): JsonResponse
    {
        $tag->load(['user', 'incidences']);

        return response()->json([
            'data' => new TagResource($tag),
        ]);
    }

    /**
     * Update a tag.
     * Update the details of an existing tag.
     * Only the creator of the tag or an admin can update it.
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the tag. Example: 1
     *
     * @bodyParam name string required The name of the tag (stored in lowercase). Example: Server
     *
     * @response 200 scenario="Tag updated" {
     *   "data": {
     *     "id": 1,
     *     "name": "server",
     *     "user_id": 1,
     *     "created_at": "2026-01-01T00:00:00Z",
     *     "updated_at": "2026-01-01T00:00:00Z",
     *     "user": {...},
     *     "incidences": [...]
     *   }
     * }
     * @response 401 scenario="Unauthorized" {
     *   "message": "Unauthenticated."
     * }
     * @response 403 scenario="Forbidden" {
     *   "message": "Unauthorized"
     * }
     * @response 404 scenario="Not found" {
     *   "message": "No query results for model [App\\Models\\Tag]"
     * }
     */
    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        if ($response = $this->authorizeOwnerOrAdmin($tag)) {
            return $response;
        }

        $tag->update([
            'name' => strtolower($request->name),
        ]);

        $tag->load(['user', 'incidences']);

        return response()->json([
            'data' => new TagResource($tag),
        ]);
    }

    /**
     * Delete a tag.
     * Delete an existing tag.
     * Only the creator of the tag or an admin can delete it.
     * Note: This removes the tag from all associated incidences (pivot table).
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the tag. Example: 1
     *
     * @response 200 scenario="Tag deleted" {
     *   "message": "Tag deleted successfully"
     * }
     * @response 401 scenario="Unauthorized" {
     *   "message": "Unauthenticated."
     * }
     * @response 403 scenario="Forbidden" {
     *   "message": "Unauthorized"
     * }
     * @response 404 scenario="Not found" {
     *   "message": "No query results for model [App\\Models\\Tag]"
     * }
     */
    public function destroy(Tag $tag): JsonResponse
    {
        if ($response = $this->authorizeOwnerOrAdmin($tag)) {
            return $response;
        }

        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully',
        ]);
    }
}
