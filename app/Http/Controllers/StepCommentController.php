<?php

namespace App\Http\Controllers;

use App\Models\Step;
use App\Models\Task;
use App\Models\StepComment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StepCommentController extends Controller
{
    public function store(Request $request, Task $task, Step $step)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        StepComment::create([
            'content' => $request->content,
            'step_id' => $step->id,
            'user_id' => Auth::id(),
        ]);

        // Log the comment
        $user = Auth::user();
        $roleLabel = $user->role ?? 'user';
        if ($roleLabel === 'super-admin') {
            $roleLabel = 'super admin';
        }

        $content = sprintf('%s commented on task', $roleLabel);

        Transaction::create([
            'content' => $content,
            'user_id' => $user->id,
            'task_id' => $task->id,
        ]);

        return back();
    }

    public function destroy(Task $task, Step $step, StepComment $comment)
    {
        // optional safety check
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();

        return back();
    }
}
