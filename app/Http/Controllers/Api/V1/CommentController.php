<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;

/**
 * @group Comments
 * Endpoints for managing comments on incidences, including listing, creating, viewing, updating, and deleting comments.
 * Returns only root comments (parent_id = null) with their nested children.
 */
class CommentController extends Controller
{
    /**
     * List comments.
     * Retrieve all root comments for a specific incidence, including their nested children.
     * 
     * @unauthenticated
     * @urlParam incidenceId int required The ID of the incidence. Example: 1
     * 
     * @response 200 scenario="Comments retrieved" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "body": "This is a comment",
     *       "user_id": 1,
     *       "incidence_id": 1,
     *       "parent_id": null,
     *       "created_at": "2026-01-01T00:00:00Z",
     *       "updated_at": "2026-01-01T00:00:00Z",
     *       "user": {...},
     *       "children": [
     *         {
     *           "id": 2,
     *           "body": "This is a reply",
     *           "user_id": 2,
     *           "incidence_id": 1,
     *           "parent_id": 1,
     *           "created_at": "2026-01-01T00:00:00Z",
     *           "updated_at": "2026-01-01T00:00:00Z",
     *           "user": {...},
     *           "children": [...]
     *         }
     *       ]
     *     }
     *   ]
     * }
     * @response 404 scenario="Incidence not found" {
     *   "message": "No query results for model [App\\Models\\Incidence]"
     * }
     */
    public function index(string $incidenceId): JsonResponse
    {
        $comments = Comment::with(['user', 'children.user'])
            ->where('incidence_id', $incidenceId)
            ->whereNull('parent_id')
            ->with('children', function ($query) {
                $query->with('children.user');
            })
            ->get();

        return response()->json([
            'data' => CommentResource::collection($comments),
        ]);
    }
    /**
     * Create a new comment.
     * Add a new comment to a specific incidence. 
     * The authenticated user will be set as the creator of the comment (user_id).
     * 
     * @authenticated
     * @urlParam incidenceId int required The ID of the incidence to comment on. Example: 1
     * @bodyParam body string required The content of the comment. Example: "This is a comment."
     * @bodyParam parent_id int optional The ID of the parent comment for replies. Example: 1
     * 
     * @response 201 scenario="Comment created" {
     *   "data": {
     *     "id": 1,
     *     "body": "This is a comment",
     *     "user_id": 1,
     *     "incidence_id": 1,
     *     "parent_id": null,
     *     "created_at": "2026-01-01T00:00:00Z",
     *     "updated_at": "2026-01-01T00:00:00Z",
     *     "user": {...}
     *   }
     * }
     * @response 401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated."
     * }
     * @response 422 scenario="Validation error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "body": ["The body field is required."],
     *     "parent_id": ["The parent_id must be a valid comment ID."]
     *   }
     * }
     */
    public function store(StoreCommentRequest $request, int $incidenceId): JsonResponse
    {
        // Validate parent_id if provided
        if ($request->parent_id) {
            $parentComment = Comment::where('id', $request->parent_id)
                ->where('incidence_id', $incidenceId)
                ->first();

            if (!$parentComment) {
                return response()->json(['message' => 'Invalid parent_id. Must belong to the same incidence.'], 422);
            }
        }

        $comment = Comment::create([
            'body' => $request->body,
            'user_id' => auth()->id(),
            'incidence_id' => $incidenceId,
            'parent_id' => $request->parent_id,
        ]);

        $comment->load('user');

        return response()->json([
            'data' => new CommentResource($comment),
        ], 201);
    }

    /**
     * View a single comment.
     * Get detailed information about a specific comment by its ID, including parent and children.
     * 
     * @unauthenticated
     * @urlParam id integer required The ID of the comment. Example: 1
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "body": "This is a comment",
     *     "user_id": 1,
     *     "incidence_id": 1,
     *     "parent_id": null,
     *     "created_at": "2026-01-01T00:00:00Z",
     *     "updated_at": "2026-01-01T00:00:00Z",
     *     "user": {...},
     *     "children": [
     *       {
     *         "id": 2,
     *         "body": "This is a reply",
     *         "user_id": 2,
     *         "incidence_id": 1,
     *         "parent_id": 1,
     *         "created_at": "2026-01-01T00:00:00Z",
     *         "updated_at": "2026-01-01T00:00:00Z",
     *         "user": {...},
     *         "children": [...]
     *       }
     *     ]
     * 
     * @response 404 scenario="Comment not found" {
     *   "message": "No query results for model [App\\Models\\Comment]"
     * }
     */
    public function show(Comment $comment): JsonResponse
    {
        $comment->load(['user', 'children.user', 'parent.user']);

        return response()->json([
            'data' => new CommentResource($comment)]);
    }

    /**
     * Update a comment.
     * Update an existing comment by its ID.
     * Only the creator of the comment can update it.
     * 
     * @authenticated
     * @urlParam id integer required The ID of the comment to update. Example: 1
     * @bodyParam body string required The updated content of the comment. Example: "This is an updated comment."
     * 
     * @response 200 scenario="Comment updated" {
     *   "data": {
     *     "id": 1,
     *     "body": "This is an updated comment",
     *     "user_id": 1,
     *     "incidence_id": 1,
     *     "parent_id": null,
     *     "created_at": "2026-01-01T00:00:00Z",
     *     "updated_at": "2026-01-01T00:00:00Z",
     *     "user": {...}
     *   }
     * }
     * @response 403 scenario="Unauthorized" {
     *   "message": "Unauthorized"
     * }
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->update([
            'body' => $request->body,
        ]);

        return response()->json([
            'data' => new CommentResource($comment),
        ]);
    }

    /**
     * Delete a comment.
     * Delete an existing comment by its ID.
     * Only the creator of the comment or an admin can delete it.
     * 
     * @authenticated
     * @urlParam id integer required The ID of the comment to delete. Example: 1
     * 
     * @response 200 scenario="Comment deleted" {
     *   "message": "Comment deleted successfully"
     * }
     * @response 401 scenario="Unauthenticated" {
     *  "message": "Unauthenticated."
     * }
     * @response 403 scenario="Unauthorized" {
     *   "message": "Unauthorized"
     * }
     * @response 404 scenario="Not Found" {
     *   "message": "No query results for model [App\\Models\\Comment]"
     * }
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $user = auth()->user();

        if (! $user->isAdmin() && $comment->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}
