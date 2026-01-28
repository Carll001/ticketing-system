<?php

namespace App\Http\Services;

use App\Models\Response;
use App\Models\Proof;
use App\Models\Attachment;
use App\Models\StepField;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ResponseService
{
    /**
     * Store step field responses and proofs with attachments
     *
     * @param array $data
     * @param Request $request
     * @return void
     */
    public function store(array $data, Request $request): void
    {
        DB::transaction(function () use ($data, $request) {

            // Get StepField
            $stepField = StepField::find($data['step_field_id']);
            if (!$stepField) {
                abort(422, 'Invalid Step Field ID. Please select a valid field.');
            }

            $stepId = $stepField->step_id;

            // Save step field responses
            if (!empty($data['response'])) {
                foreach ($data['response'] as $fieldId => $value) {
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
            if (!empty($data['description']) || $request->hasFile('attachments')) {

                $proof = Proof::create([
                    'id'          => Str::uuid(),
                    'step_id'     => $stepId,
                    'user_id'     => Auth::id(),
                    'description' => $data['description'] ?? null,
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
    }
}
