<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresetRequest;
use App\Models\Preset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PresetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presets = Preset::all();

        return Inertia::render('Preset/Index', [
            'presets' => $presets,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Preset/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PresetRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $preset = Preset::create([
                'user_id' => Auth::id(),
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'has_cost' => $data['has_cost'] ?? false,
            ]);

            if (!empty($data['fields'])) {
                foreach ($data['fields'] as $field) {
                    $preset->fields()->create([
                        'type' => $field['type'],
                        'label' => $field['label'],
                    ]);
                }
            }
        });

        return redirect()->route('preset.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Preset $preset)
    {
        $preset->load('fields');

        return Inertia::render('Preset/Show', [
            'preset' => $preset,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Preset $preset)
    {
        $preset->load('fields');

        return Inertia::render('Preset/Edit', [
            'preset' => $preset,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PresetRequest $request, Preset $preset)
    {
        $data = $request->validated();

        DB::transaction(function () use ($preset, $data) {
            // Update parent preset
            $preset->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'has_cost' => $data['has_cost'] ?? false,
            ]);

            $fields = collect($data['fields'] ?? []);
            $incomingIds = $fields->pluck('id')->filter()->toArray();

            // Delete removed fields
            $preset->fields()->whereNotIn('id', $incomingIds)->delete();

            // Update existing or create new fields
            foreach ($fields as $field) {
                $preset->fields()->updateOrCreate(
                    ['id' => $field['id'] ?? null],
                    [
                        'type' => $field['type'],
                        'label' => $field['label'],
                    ]
                );
            }
        });

        return redirect()->route('preset.index')->with('success', 'Preset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Preset $preset)
    {
        DB::transaction(function () use ($preset) {
            $preset->fields()->delete();
            $preset->delete();
        });

        return redirect()->route('preset.index')->with('success', 'Preset deleted successfully.');
    }
}
