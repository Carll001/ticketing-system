<?php

namespace App\Http\Controllers;

use App\Models\Preset;
use Illuminate\Http\Request;
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
    public function store(Request $request)
    {
        dd($request->all());
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
