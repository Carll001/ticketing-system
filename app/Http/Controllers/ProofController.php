<?php

namespace App\Http\Controllers;

use App\Models\Proof;
use App\Models\Attachment;
use App\Models\Task;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProofController extends Controller
{
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

    public function store(Request $request, Task $task, Step $step)
    {
        $request->validate([
            'description'   => ['nullable', 'string'],
            'attachments.*' => [ 'max:20480'],
        ]);

        if (!$request->filled('description') && !$request->hasFile('attachments')) {
            return back()->withErrors(['proof' => 'Nothing to submit.']);
        }

        DB::transaction(function () use ($request, $step) {

            // ✅ Make sure step exists
            if (!$step) {
                throw new \Exception('Invalid step.');
            }

            $proof = Proof::create([
                'step_id'     => $step->id,  // required
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
        });

        return back()->with('success', 'Proof submitted successfully.');
    }

    public function show(Task $task, Step $step, Proof $proof)
    {
        $proof->load(['attachments', 'user']);

        return inertia('Proof/Show', [
            'task'  => $task,
            'step'  => $step,
            'proof' => $proof,
        ]);
    }

    public function edit(Task $task, Step $step, Proof $proof)
    {
        return inertia('Proof/Edit', [
            'task'  => $task,
            'step'  => $step,
            'proof' => $proof->load('attachments'),
        ]);
    }

    public function update(Request $request, Task $task, Step $step, Proof $proof)
    {
        $request->validate([
            'description' => ['nullable', 'string'],
        ]);

        $proof->update([
            'description' => $request->description,
        ]);

        return back()->with('success', 'Proof updated successfully.');
    }

    public function destroy(Task $task, Step $step, Proof $proof)
    {
        $proof->delete();

        return back()->with('success', 'Proof deleted successfully.');
    }
}
