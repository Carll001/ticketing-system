<?php

namespace App\Http\Services;

use App\Models\Preset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresetService
{
    /**
     * Create a new preset with fields
     */
    public function create(array $data): Preset
    {
        return DB::transaction(function () use ($data) {
            $preset = Preset::create([
                'user_id'     => Auth::id(),
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'has_cost'    => $data['has_cost'] ?? false,
            ]);

            if (!empty($data['fields'])) {
                foreach ($data['fields'] as $field) {
                    $preset->fields()->create([
                        'type'  => $field['type'],
                        'label' => $field['label'],
                    ]);
                }
            }

            return $preset->load('fields');
        });
    }

    /**
     * Update a preset with fields
     */
    public function update(Preset $preset, array $data): Preset
    {
        return DB::transaction(function () use ($preset, $data) {
            $preset->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'has_cost'    => $data['has_cost'] ?? false,
            ]);

            $incomingFields = collect($data['fields'] ?? []);
            $incomingIds = $incomingFields->pluck('id')->filter()->toArray();

            // Delete fields not in incoming data
            $preset->fields()->whereNotIn('id', $incomingIds)->delete();

            // Update or create fields
            foreach ($incomingFields as $field) {
                if (!empty($field['id'])) {
                    $preset->fields()->updateOrCreate(
                        ['id' => $field['id']],
                        [
                            'type'  => $field['type'],
                            'label' => $field['label'],
                        ]
                    );
                } else {
                    $preset->fields()->create([
                        'type'  => $field['type'],
                        'label' => $field['label'],
                    ]);
                }
            }

            return $preset->load('fields');
        });
    }

    /**
     * Delete a preset and its fields
     */
    public function delete(Preset $preset): void
    {
        DB::transaction(function () use ($preset) {
            $preset->fields()->delete();
            $preset->delete();
        });
    }
}
