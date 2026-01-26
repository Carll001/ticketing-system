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
            'due_date' => $this->due_date?->toISOString(),

            'steps' => StepResource::collection($this->whenLoaded('steps')),
            'assigned' => DepartmentResource::make($this->whenLoaded('assigned')),
        ];
    }
}
