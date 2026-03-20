<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
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
            'data' => $comments,
        ]);
    }

    public function store(StoreCommentRequest $request, int $incidenceId): JsonResponse
    {
        $comment = Comment::create([
            'body' => $request->body,
            'user_id' => auth()->id(),
            'incidence_id' => $incidenceId,
            'parent_id' => $request->parent_id,
        ]);

        $comment->load('user');

        return response()->json([
            'data' => $comment,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $comment = Comment::with(['user', 'children.user', 'parent.user'])->findOrFail($id);

        return response()->json([
            'data' => $comment,
        ]);
    }

    public function update(UpdateCommentRequest $request, int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->update([
            'body' => $request->body,
        ]);

        return response()->json([
            'data' => $comment,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
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
