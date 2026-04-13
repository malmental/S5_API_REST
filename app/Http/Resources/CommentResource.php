<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'user_id' => $this->user_id,
            'incidence_id' => $this->incidence_id,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'children' => CommentResource::collection($this->whenLoaded('children')),
            'parent' => new CommentResource($this->whenLoaded('parent')),
        ];
    }
}
