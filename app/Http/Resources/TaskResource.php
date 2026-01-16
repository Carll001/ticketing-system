<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description ?? 'No description',
            'assigned_to' => $this->assigned_to ?? 'Anyone',
            'creator_id' => $this->creator_id,
            'due_date' => $this->due_date?->toDateString() ?? 'No due date',
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
