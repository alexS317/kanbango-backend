<?php

namespace App\Http\Resources\Task;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'board' => $this->board,
            'created_by' => new UserResource($this->createdBy),
            'assigned_to' => new UserResource($this->assignedTo),
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status?->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
