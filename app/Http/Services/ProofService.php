<?php

namespace App\Http\Services;

use App\Models\Proof;
use App\Models\Attachment;
use App\Models\Step;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class ProofService
{
    /**
     * Create a proof with optional attachments.
     *
     * @param Step $step
     * @param array $data
     * @param UploadedFile[] $files
     * @return Proof
     */
    public function create(Step $step, array $data, array $files = []): Proof
    {
        return DB::transaction(function () use ($step, $data, $files): Proof {
            $proof = Proof::create([
                'step_id'     => $step->id,
                'user_id'     => Auth::id(),
                'description' => $data['description'] ?? null,
            ]);

            // Handle attachments
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $path = $file->store('proofs', 'public');

                    Attachment::create([
                        'proof_id'      => $proof->id,
                        'original_name' => $file->getClientOriginalName(),
                        'path'          => $path,
                        'mime'          => $file->getMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
            }

            return $proof->load('attachments', 'user');
        });
    }

    /**
     * Update a proof.
     *
     * @param Proof $proof
     * @param array|null $data Nullable for optional update
     * @return Proof
     */
    public function update(Proof $proof, ?array $data = null): Proof
    {
        $proof->update([
            'description' => $data['description'] ?? $proof->description,
        ]);

        return $proof;
    }

    /**
     * Delete a proof.
     *
     * @param Proof $proof
     * @param Step|null $step Optional step for safety check
     * @return void
     */
    public function delete(Proof $proof, ?Step $step = null): void
    {
        if ($step && $proof->step_id !== $step->id) {
            abort(403, 'Proof does not belong to this step.');
        }

        $proof->delete();
    }

    /**
     * Get all proofs for a step.
     *
     * @param Step $step
     * @return Collection<int, Proof>
     */
    public function getProofs(Step $step): Collection
    {
        return Proof::with(['attachments', 'user'])
            ->where('step_id', $step->id)
            ->latest()
            ->get();
    }
}
