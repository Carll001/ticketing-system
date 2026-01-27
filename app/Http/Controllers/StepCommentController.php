<?php

namespace App\Http\Controllers;

use App\Models\Step;
use App\Models\Task;
use App\Models\StepComment;
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
