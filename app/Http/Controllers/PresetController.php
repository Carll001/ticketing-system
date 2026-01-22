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
        // $presets = Preset::all();
        return Inertia::render('Preset/Index', [
            // 'presets' => $presets,
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Preset $preset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Preset $preset)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Preset $preset)
    {
        //
    }
}
