<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Proof;
use App\Models\Attachment;
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
        'step_field_id' => ['required', 'uuid'], 
        'response'      => ['nullable', 'array'],
        'description'   => ['nullable', 'string'],
        'attachments.*' => ['file', 'max:20480'], 
    ]);

    DB::transaction(function () use ($validated, $request) {

        // =========================
        // GET STEP FIELD
        // =========================
        $stepField = StepField::find($validated['step_field_id']);

        if (!$stepField) {
            // Fail gracefully if invalid
            return back()->withErrors([
                'step_field_id' => 'Invalid Step Field ID. Please select a valid field.'
            ])->throwResponse(); // Laravel will stop execution here
        }

        $stepId = $stepField->step_id; // Get the actual step_id

        // =========================
        // SAVE STEP FIELD RESPONSES
        // =========================
        if (!empty($validated['response'])) {
            foreach ($validated['response'] as $fieldId => $value) {
                Response::updateOrCreate(
                    [
                        'step_field_id' => $fieldId,
                        'user_id'       => Auth::id(),
                    ],
                    [
                        'id'       => Str::uuid(),
                        'response' => is_bool($value) ? ($value ? 'true' : 'false') : $value,
                    ]
                );
            }
        }

        // =========================
        // SAVE PROOF (if any)
        // =========================
        if ($request->filled('description') || $request->hasFile('attachments')) {

            $proof = Proof::create([
                'id'          => Str::uuid(),
                'step_id'     => $stepId,  // always valid
                'user_id'     => Auth::id(),
                'description' => $request->description,
            ]);

            // =========================
            // SAVE ATTACHMENTS
            // =========================
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('proofs', 'public');

                    Attachment::create([
                        'id'            => Str::uuid(),
                        'proof_id'      => $proof->id,
                        'original_name' => $file->getClientOriginalName(),
                        'path'          => $path,
                        'mime'          => $file->getMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
            }
        }
    });

    return back()->with('success', 'Form submitted successfully!');
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
