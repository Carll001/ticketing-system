<?php

namespace App\Http\Services;

use App\Models\Step;
use Illuminate\Support\Facades\DB;

class StepService
{
    /**
     * Create a new Step along with its fields
     *
     * @param array $data
     * @return Step
     */
    public function store(array $data): Step
    {
        return DB::transaction(function () use ($data) {
            $step = Step::create([
                'task_id'     => $data['task_id'],
                'preset_id'   => $data['preset_id'] ?? null,
                'title'       => $data['title'],
                'description' => $data['description'],
                'assigned_to' => $data['assigned_to'],
                'status'      => !empty($data['assigned_to']) ? 'assigned' : 'pending',
            ]);

            if (!empty($data['fields'])) {
                foreach ($data['fields'] as $field) {
                    $step->fields()->create([
                        'type'  => $field['type'],
                        'label' => $field['label'],
                    ]);
                }
            }

            return $step;
        });
    }

    /**
     * Update an existing Step along with its fields
     *
     * @param Step $step
     * @param array $data
     * @return Step
     */
    public function update(Step $step, array $data): Step
    {
        $data['status'] = !empty($data['assigned_to']) ? 'assigned' : 'pending';

        DB::transaction(function () use ($step, $data) {
            $step->update([
                'title'       => $data['title'],
                'description' => $data['description'],
                'assigned_to' => $data['assigned_to'],
                'status'      => $data['status'],
            ]);

            // Delete old fields and insert new ones
            $step->fields()->delete();

            if (!empty($data['fields'])) {
                foreach ($data['fields'] as $field) {
                    $step->fields()->create([
                        'type'  => $field['type'],
                        'label' => $field['label'],
                    ]);
                }
            }
        });

        return $step;
    }

    /**
     * Delete a Step
     *
     * @param Step $step
     * @return void
     */
    public function delete(Step $step): void
    {
        $step->delete();
    }
}
