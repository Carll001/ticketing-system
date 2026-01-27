<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresetRequest;
use App\Models\Preset;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Services\PresetService;

class PresetController extends Controller
{
    protected PresetService $presetService;

    public function __construct(PresetService $presetService)
    {
        $this->presetService = $presetService;
    }

    public function index()
    {
        $presets = Preset::with('fields')->get();

        return Inertia::render('Preset/Index', [
            'presets' => $presets,
        ]);
    }

    public function create()
    {
        return Inertia::render('Preset/Create');
    }

    public function store(PresetRequest $request)
    {
        $preset = $this->presetService->create($request->validated());

        return redirect()->route('preset.index')
            ->with('success', 'Preset created successfully.');
    }

    public function show(Preset $preset)
    {
        $preset->load('fields');

        return Inertia::render('Preset/Show', [
            'preset' => $preset,
        ]);
    }

    public function edit(Preset $preset)
    {
        $preset->load('fields');

        return Inertia::render('Preset/Edit', [
            'preset' => $preset,
        ]);
    }

    public function update(PresetRequest $request, Preset $preset)
    {
        $preset = $this->presetService->update($preset, $request->validated());

        return redirect()->route('preset.index')
            ->with('success', 'Preset updated successfully.');
    }

    public function destroy(Preset $preset)
    {
        $this->presetService->delete($preset);

        return redirect()->route('preset.index')
            ->with('success', 'Preset deleted successfully.');
    }
}
