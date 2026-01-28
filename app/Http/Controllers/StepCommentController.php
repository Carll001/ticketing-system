<?php

namespace App\Http\Controllers;

use App\Models\Step;
use App\Models\Task;
use App\Models\StepComment;
use App\Http\Requests\StepCommentRequest;
use Illuminate\Support\Facades\Auth;

class StepCommentController extends Controller
{
    /**
     * Store a new comment for a step.
     */
    public function store(StepCommentRequest $request, Task $task, Step $step)
    {
        StepComment::create([
            'content' => $request->validated()['content'],
            'step_id' => $step->id,
            'user_id' => Auth::id(),
        ]);

        return back();
    }

    /**
     * Delete a comment.
     */
    public function destroy(Task $task, Step $step, StepComment $comment)
    {
        // Safety check: only owner can delete
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return back();
    }
}
