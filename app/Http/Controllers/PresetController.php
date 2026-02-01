<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresetRequest;
use App\Models\Preset;
use App\Models\PresetField;
use Illuminate\Http\Request;
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
        $presets = Preset::paginate(8);
        return Inertia::render('Preset/Index', [
            'presets' => [
                'data' => $presets->items(),
                'current_page' => $presets->currentPage(),
                'last_page' => $presets->lastPage(),
                'per_page' => $presets->perPage(),
                'total' => $presets->total(),
                'from' => $presets->firstItem(),
                'to' => $presets->lastItem(),
            ],
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
            // 1. Create the Parent
            $preset = Preset::create([
                'user_id' => Auth::id(),
                'name'        => $data['name'],
                'description' => $data['description'],
                'has_cost'    => $data['has_cost'] ?? false,
            ]);

            // 2. Create the Fields
            if (!empty($data['fields'])) {
                foreach ($data['fields'] as $field) {
                    $preset->fields()->create([
                        'type'  => $field['type'],
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
        $preset->load(['fields']);
        return Inertia::render('Preset/Show', [
            'preset' => $preset,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Preset $preset)
    {
        $preset->load(['fields']);
        return Inertia::render('Preset/Edit', [
            'preset' => $preset,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Preset $preset)
    {
        // 1. Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'array',
            'fields.*.id' => 'required', // This is the UUID from the frontend
            'fields.*.type' => 'required|in:Checkbox,Input,Description',
            'fields.*.label' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($preset, $validated) {
            // 2. Update the parent Preset
            $preset->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            // 3. Handle the Fields (Syncing)
            $incomingFields = collect($validated['fields']);
            $incomingIds = $incomingFields->pluck('id')->toArray();

            // Delete fields that are no longer present in the form
            $preset->fields()->whereNotIn('id', $incomingIds)->delete();

            // Update existing fields or Create new ones
            foreach ($incomingFields as $fieldData) {
                $preset->fields()->updateOrCreate(
                    ['id' => $fieldData['id']], // Unique identifier
                    [
                        'type' => $fieldData['type'],
                        'label' => $fieldData['label'],
                    ]
                );
            }
        });

        // return redirect()->back()->with('success', 'Preset updated successfully.');
        return redirect()->route('preset.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Preset $preset)
    {
        dd('delete');
    }
}
