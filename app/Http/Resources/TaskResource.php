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

            // FIXED: Sort steps by position before transforming
            'steps' => StepResource::collection(
                $this->whenLoaded('steps', function () {
                    return $this->steps->sortBy('position')->values();
                })
            ),
            'assigned' => DepartmentResource::make($this->whenLoaded('assigned')),
            
            // Cost calculations with formatting
            'total_steps_cost' => $this->when($this->relationLoaded('steps'), function () {
                return $this->steps->sum('cost');
            }),
            
            'total_steps_cost_formatted' => $this->when($this->relationLoaded('steps'), function () {
                return '₱' . number_format($this->steps->sum('cost'), 2);
            }),
            
            
            'steps_cost_summary' => $this->when($this->relationLoaded('steps'), function () {
                $stepsWithCost = $this->steps->where('has_cost', true);
                $total = $this->steps->sum('cost');
                $average = $stepsWithCost->count() > 0 ? $total / $stepsWithCost->count() : 0;
                
                return [
                    'total' => $total,
                    'total_formatted' => '₱' . number_format($total, 2),
                    'count' => $stepsWithCost->count(),
                    'average' => $average,
                    'average_formatted' => '₱' . number_format($average, 2),
                ];
            }),
        ];
    }
}