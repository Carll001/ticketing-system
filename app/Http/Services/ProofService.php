<?php

namespace App\Services;

use App\Models\Proof;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ProofService
{
    public function create(array $data): Proof
    {
        $proof = Proof::create($data);

        // Log the proof submission
        $user = Auth::user();
        $roleLabel = $user->role ?? 'user';
        if ($roleLabel === 'super-admin') {
            $roleLabel = 'super admin';
        }

        $content = sprintf('%s submitted proof', $roleLabel);

        // Get task_id from the step if available
        if ($proof->step && $proof->step->task) {
            $taskId = $proof->step->task->id;
        } else {
            $taskId = null;
        }

        Transaction::create([
            'content' => $content,
            'user_id' => $user->id,
            'task_id' => $taskId,
        ]);

        return $proof;
    }

    public function update(Proof $proof, array $data): Proof
    {
        $proof->update($data);
        return $proof;
    }

    public function delete(Proof $proof): void
    {
        $proof->delete();
    }
}
