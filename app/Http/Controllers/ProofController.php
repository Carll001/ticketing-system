<?php

namespace App\Http\Controllers;

use App\Models\Proof;
use App\Models\Step;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Services\ProofService;

class ProofController extends Controller
{
    protected ProofService $proofService;

    public function __construct(ProofService $proofService)
    {
        $this->proofService = $proofService;
    }

    /**
     * Display all proofs for a step
     */
    public function index(Task $task, Step $step)
    {
        $proofs = $this->proofService->getProofs($step);

        return inertia('Proof/Index', [
            'task'   => $task,
            'step'   => $step,
            'proofs' => $proofs,
        ]);
    }

    /**
     * Store a new proof with optional attachments
     */
    public function store(Request $request, Task $task, Step $step)
    {
        $validated = $request->validate([
            'description'   => ['nullable', 'string'],
            'attachments.*' => ['file', 'max:20480'],
        ]);

        if (!$request->filled('description') && !$request->hasFile('attachments')) {
            return back()->withErrors(['proof' => 'Nothing to submit.']);
        }

        $proof = $this->proofService->create(
            $step,
            $validated,
            $request->file('attachments', [])
        );

        return back()->with([
            'success' => 'Proof submitted successfully.',
            'proof'   => $proof,
        ]);
    }

    /**
     * Show a specific proof
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
     * Show the edit form for a proof
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
     * Update a proof's description
     */
    public function update(Request $request, Task $task, Step $step, Proof $proof)
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string'],
        ]);

        $this->proofService->update($proof, $validated);

        return back()->with('success', 'Proof updated successfully.');
    }

    /**
     * Delete a proof (with step safety check)
     */
    public function destroy(Task $task, Step $step, Proof $proof)
    {
        $this->proofService->delete($proof, $step);

        return response()->json(['message' => 'Proof deleted']);
    }
}
