<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::with(['user', 'incidences'])->get();

        return response()->json([
            'data' => $tags,
        ]);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = Tag::firstOrCreate(
            ['name' => strtolower($request->name)],
            ['user_id' => auth()->id()]
        );

        return response()->json([
            'data' => $tag->load(['user', 'incidences']),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $tag = Tag::with(['user', 'incidences'])->findOrFail($id);

        return response()->json([
            'data' => $tag,
        ]);
    }

    public function update(UpdateTagRequest $request, int $id): JsonResponse
    {
        $tag = Tag::findOrFail($id);

        $user = auth()->user();

        if (!$user->isAdmin() && $tag->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tag->update([
            'name' => strtolower($request->name),
        ]);

        return response()->json([
            'data' => $tag->load(['user', 'incidences']),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $tag = Tag::findOrFail($id);

        $user = auth()->user();

        if (!$user->isAdmin() && $tag->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully',
        ]);
    }
}
