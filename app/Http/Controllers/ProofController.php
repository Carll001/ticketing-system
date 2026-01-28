<?php

namespace App\Http\Controllers;

use App\Models\Proof;
use App\Models\Attachment;
use App\Models\Task;
use App\Models\Step;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProofRequest;

class ProofController extends Controller
{
    /**
     * List all proofs for a step.
     */
    public function index(Task $task, Step $step)
    {
        $proofs = Proof::with(['attachments', 'user'])
            ->where('step_id', $step->id)
            ->latest()
            ->get();

        return inertia('Proof/Index', [
            'task'   => $task,
            'step'   => $step,
            'proofs' => $proofs,
        ]);
    }

    /**
     * Store a new proof.
     */
    public function store(ProofRequest $request, Task $task, Step $step)
    {
        if (!$request->filled('description') && !$request->hasFile('attachments')) {
            return back()->withErrors(['proof' => 'Nothing to submit.']);
        }

        $proof = DB::transaction(function () use ($request, $step) {

            $proof = Proof::create([
                'step_id'     => $step->id,
                'user_id'     => Auth::id(),
                'description' => $request->description,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
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

            return $proof;
        });

        return back()->with([
            'success' => 'Proof submitted successfully.',
            'proof'   => $proof->load('attachments', 'user'),
        ]);
    }

    /**
     * Show a single proof.
     */
    public function show(Task $task, Step $step, Proof $proof)
    {
        $proof->load(['attachments', 'user']);

        return inertia('Proof/Show', [
            'task'  => $task,
            'step'  => $step,
            'proof' => $proof,
        ]);
    }

    /**
     * Show form to edit a proof.
     */
    public function edit(Task $task, Step $step, Proof $proof)
    {
        return inertia('Proof/Edit', [
            'task'  => $task,
            'step'  => $step,
            'proof' => $proof->load('attachments'),
        ]);
    }

    /**
     * Update proof description.
     */
    public function update(ProofRequest $request, Task $task, Step $step, Proof $proof)
    {
        $proof->update([
            'description' => $request->description,
        ]);

        return back()->with('success', 'Proof updated successfully.');
    }

    /**
     * Delete a proof.
     */
    public function destroy(Task $task, Step $step, Proof $proof)
    {
        if ($proof->step_id !== $step->id) {
            return response()->json(['message' => 'Proof does not belong to this step'], 403);
        }

        $proof->delete();

        return response()->json(['message' => 'Proof deleted']);
    }
}
