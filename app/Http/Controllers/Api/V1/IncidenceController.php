<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AuthorizesUser;
use App\Http\Requests\StoreIncidenceRequest;
use App\Http\Requests\UpdateIncidenceRequest;
use App\Http\Resources\IncidenceResource;
use App\Models\Incidence;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @group Incidences
 * Endpoints for managing incidences, including listing, creating, viewing, updating, and deleting incidences.
 */
class IncidenceController extends Controller
{
    use AuthorizesUser;

    /**
     * List all incidences.
     * Retrieve all incidences with optional filters.
     *
     * @unauthenticated
     *
     * @queryParam status string Filter by status (open, in_progress, closed).
     * @queryParam priority string Filter by priority (low, medium, high).
     * @queryParam tags string Filter by comma-separated list of tag IDs. Example: 1, 2, 3
     * @queryParam search string Search by title or description. Examplo: server
     *
     * @response 200 scenario="Incidences retrieved" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Server Down",
     *       "description": "Main server not responding",
     *       "status": "open",
     *       "priority": "critical",
     *       "user_id": 1,
     *       "assigned_to": 2,
     *       "created_at": "2026-01-01T00:00:00Z",
     *       "updated_at": "2026-01-01T00:00:00Z",
     *       "user": {...},
     *       "assigned_user": {...},
     *       "tags": [...]
     *     }
     *   ]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Incidence::with(['user', 'assignedUser', 'tags', 'comments.user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('tags')) {
            $tagIds = explode(',', $request->tags);
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('tags', function ($tagQuery) use ($search) {
                        $tagQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = min($request->per_page ?? 10, 100);

        $incidences = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => IncidenceResource::collection($incidences),
            'meta' => [
                'current_page' => $incidences->currentPage(),
                'last_page' => $incidences->lastPage(),
                'per_page' => $incidences->perPage(),
                'total' => $incidences->total(),
            ],
        ]);
    }

    /**
     * List my incidences.
     * Retrieve incidences created by the authenticated user.
     *
     * @authenticated
     *
     * @queryParam status string Filter by status (open, in_progress, closed).
     * @queryParam priority string Filter by priority (low, medium, high).
     * @queryParam search string Search by title or description.
     *
     * @response 200 scenario="Incidences retrieved" {
     *   "data": [...],
     *   "meta": {...}
     * }
     * @response 401 scenario="Unauthenticated" {
     *  "message": "Unauthenticated."
     * }
     */
    public function myIncidences(Request $request): JsonResponse
    {
        $query = Incidence::with(['user', 'assignedUser', 'tags', 'comments.user'])
            ->where('user_id', auth()->id());

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('tags', function ($tagQuery) use ($search) {
                        $tagQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = min($request->per_page ?? 10, 100);

        $incidences = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => IncidenceResource::collection($incidences),
            'meta' => [
                'current_page' => $incidences->currentPage(),
                'last_page' => $incidences->lastPage(),
                'per_page' => $incidences->perPage(),
                'total' => $incidences->total(),
            ],
        ]);
    }

    /**
     * Create a new incidence.
     * Create a new incidence with the provided details.
     * The authenticated user will be set as the creator of the incidence (user_id).
     *
     * @authenticated
     *
     * @bodyParam title string required The title of the incidence. Example: "Server Down"
     * @bodyParam description string required A detailed description of the incidence. Example: "The main server is not responding since 3 PM."
     * @bodyParam status string with the status of the incidence. Allowed values: open, in_progress, closed. Default is "open".
     * @bodyParam priority string with the priority level of the incidence. Allowed values: low, medium, high. Default is "medium".
     * @bodyParam assigned_to integer ID of the user assigned to handle this incidence. Example: 2
     * @bodyParam tags string A comma-separated list of tags to associate with the incidence. Example: "server, urgent, backend"
     *
     * @response 201 scenario="Incidence created" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Server Down",
     *       "description": "Main server not responding",
     *       "status": "open",
     *       "priority": "critical",
     *       "user_id": 1,
     *       "assigned_to": 2,
     *       "created_at": "2026-01-01T00:00:00Z",
     *       "updated_at": "2026-01-01T00:00:00Z",
     *       "user": {...},
     *       "assigned_user": {...},
     *       "tags": [...]
     *     }
     *   ]
     * }
     * @response 401 scenario="Unauthenticated" {
     *  "message": "Unauthenticated."
     * }
     * @response 422 scenario="Validation error" [
     *  {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "title": ["The title field is required."],
     *     "description": ["The description field is required."]
     *   }
     *  }
     * ]
     */
    public function store(StoreIncidenceRequest $request): JsonResponse
    {
        $incidence = Incidence::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'open',
            'priority' => $request->priority ?? 'medium',
            'user_id' => auth()->id(),
            'assigned_to' => $request->assigned_to,
        ]);

        if ($request->tags) {
            $this->syncTags($incidence, $request->tags);
        }

        $incidence->load(['user', 'assignedUser', 'tags']);

        return response()->json([
            'data' => $incidence,
        ], 201);
    }

    /**
     * View a single incidence.
     * Get detailed information about a specific incidence by its ID.
     *
     * @unauthenticated
     *
     * @urlParam id integer required The ID of the incidence. Example: 1
     *
     * @response 200 scenario="Incidence retrieved" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Server Down",
     *       "description": "Main server not responding",
     *       "status": "open",
     *       "priority": "critical",
     *       "user_id": 1,
     *       "assigned_to": 2,
     *       "created_at": "2026-01-01T00:00:00Z",
     *       "updated_at": "2026-01-01T00:00:00Z"
     *     }
     *   ]
     * }
     */
    public function show(Incidence $incidence): JsonResponse
    {
        $incidence->load(['user', 'assignedUser', 'tags', 'comments.user']);

        return response()->json([
            'data' => new IncidenceResource($incidence),
        ]);
    }

    /**
     * Update an incidence.
     * Update an existing incidence by its ID.
     * Only the creator of the incidence or an admin can update it.
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the incidence to update. Example: 1
     *
     * @bodyParam title string The title of the incidence. Example: "Server Down - Updated"
     * @bodyParam description string The description of the incidence. Example: "Main server not responding - Updated"
     * @bodyParam status string The status of the incidence. Example: "in_progress"
     * @bodyParam priority string The priority of the incidence. Example: "high"
     * @bodyParam assigned_to integer The ID of the user assigned to handle this incidence. Example: 2
     * @bodyParam tags string A comma-separated list of tags to associate with the incidence. Example: "server, urgent, backend"
     *
     * @response 200 scenario="Incidence updated" {
     *  "data": [
     *      {
     *        "id": 1,
     *        "title": "Server Down - Updated",
     *        "description": "Main server not responding - Updated",
     *        "status": "in_progress",
     *        "priority": "high",
     *        "user_id": 1,
     *        "assigned_to": 2,
     *        "created_at": "2026-01-01T00:00:00Z",
     *        "updated_at": "2026-01-01T01:00:00Z"
     *      }
     *  ]
     * }
     * @response 401 scenario="Unauthenticated" {
     *  "message": "Unauthenticated."
     * }
     * @response 403 scenario="Unauthorized" {
     *  "message": "Unauthorized"
     * }
     * @response 400 scenario="Invalid request data" {
     *  "message": "Invalid request data. No query results for model [App\\Models\\Incidence]."
     * }
     */
    public function update(UpdateIncidenceRequest $request, Incidence $incidence): JsonResponse
    {
        if ($response = $this->authorizeOwnerOrAdmin($incidence)) {
            return $response;
        }

        $incidence->update($request->except('tags'));

        if ($request->has('tags')) {
            $this->syncTags($incidence, $request->tags);
        }

        $incidence->load(['user', 'assignedUser', 'tags']);

        return response()->json([
            'data' => $incidence,
        ]);
    }

    /**
     * Delete an incidence.
     * Delete an existing incidence by its ID.
     * Only the creator of the incidence or an admin can delete it.
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the incidence to delete. Example: 1
     *
     * @response 200 scenario="Incidence deleted" {
     *  "message": "Incidence deleted successfully"
     * }
     * @response 401 scenario="Unauthenticated" {
     *  "message": "Unauthenticated."
     * }
     * @response 403 scenario="Unauthorized" {
     *  "message": "Unauthorized"
     * }
     */
    public function destroy(Incidence $incidence): JsonResponse
    {
        if ($response = $this->authorizeOwnerOrAdmin($incidence)) {
            return $response;
        }

        $incidence->delete();

        return response()->json([
            'message' => 'Incidence deleted successfully',
        ]);
    }

    private function syncTags(Incidence $incidence, string $tagsString): void
    {
        $tagNames = array_filter(array_map('trim', explode(',', $tagsString)));
        $tagNames = array_slice($tagNames, 0, 10);
        $tagIds = [];

        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(['name' => Str::slug($name)]);
            $tagIds[] = $tag->id;
        }

        $incidence->tags()->sync($tagIds);
    }
}
