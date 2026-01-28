<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Proof;
use App\Models\Attachment;
use App\Models\StepField;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ResponseRequest;

class ResponseController extends Controller
{
    /**
     * Store a newly created response in storage.
     */
    public function store(ResponseRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {

            // Get StepField
            $stepField = StepField::findOrFail($validated['step_field_id']);
            $stepId = $stepField->step_id;

            // Save field responses
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

            // Save proof if description or attachments exist
            if ($request->filled('description') || $request->hasFile('attachments')) {
                $proof = Proof::create([
                    'id'          => Str::uuid(),
                    'step_id'     => $stepId,
                    'user_id'     => Auth::id(),
                    'description' => $request->description,
                ]);

                // Save attachments
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
     * Optional: implement these later if needed.
     */
    public function index() {}
    public function create() {}
    public function show(Response $response) {}
    public function edit(Response $response) {}
    public function update(ResponseRequest $request, Response $response) {}
    public function destroy(Response $response) {}
}
