<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pivot->id,
            'user' => new UserResource($this->resource),
            'role' => $this->pivot->role,
            'created_at' => $this->pivot->created_at,
            'updated_at' => $this->pivot->updated_at,
        ];
    }
}
