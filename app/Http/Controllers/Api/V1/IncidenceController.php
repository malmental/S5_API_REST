<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidenceRequest;
use App\Http\Requests\UpdateIncidenceRequest;
use App\Models\Incidence;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IncidenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Incidence::with(['user', 'assignedUser', 'tags']);
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
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $incidences = $query->get();

        return response()->json([
            'data' => $incidences,
        ]);
    }

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

    public function show(int $id): JsonResponse
    {
        $incidence = Incidence::with(['user', 'assignedUser', 'tags'])->findOrFail($id);

        return response()->json([
            'data' => $incidence,
        ]);
    }
    public function update(UpdateIncidenceRequest $request, int $id): JsonResponse
    {
        $incidence = Incidence::findOrFail($id);
        
        $user = auth()->user();

        if (!$user->isAdmin() && $incidence->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
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

    public function destroy(int $id): JsonResponse
    {
        $incidence = Incidence::findOrFail($id);
        $user = auth()->user();

        if (!$user->isAdmin() && $incidence->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $incidence->delete();

        return response()->json([
            'message' => 'Incidence deleted successfully',
        ]);
    }

    private function syncTags(Incidence $incidence, string $tagsString): void
    {
        $tagNames = array_filter(array_map('trim', explode(',', $tagsString)));
        $tagIds = [];

        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(['name' => Str::slug($name)]);
            $tagIds[] = $tag->id;
        }

        $incidence->tags()->sync($tagIds);
    }
}
