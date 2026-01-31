<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Proof;
use App\Models\Attachment;
use App\Models\Step;
use App\Models\StepField;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'step_field_id' => 'required|uuid', // This is actually the Step ID
            'response'      => 'required|array',
            'cost'          => 'nullable|numeric|min:0',
        ]);


        // Save field responses
        foreach ($validated['response'] as $fieldId => $value) {
            \App\Models\Response::updateOrCreate(
                [
                    'step_field_id' => $fieldId, // The ID of the specific field
                    'user_id'       => Auth::id(),
                ],
                [
                    'id'       => Str::uuid(),
                    'response' => is_bool($value) ? ($value ? 'true' : 'false') : $value,
                ]
            );
        }
     
        // Save cost to the Step if provided
        if (isset($validated['cost'])) {
            $step = Step::find($validated['step_field_id']);
            
            if ($step) {
                $step->update([
                    'cost' => number_format((float)$request->cost, 2, '.', ''),
                ]);
            }
        }

        return back()->with('success', 'Response submitted successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Response $response)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Response $response)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Response $response)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Response $response)
    {
        //
    }
}
