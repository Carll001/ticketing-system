<?php

namespace App\Http\Controllers;

use App\Models\Step;
use App\Models\Task;
use App\Models\StepComment;
use Illuminate\Http\Request;
use App\Http\Services\StepCommentService;

class StepCommentController extends Controller
{
    protected StepCommentService $commentService;

    public function __construct(StepCommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function store(Request $request, Task $task, Step $step)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $this->commentService->store($step, $validated);

        return back();
    }

    public function destroy(Task $task, Step $step, StepComment $comment)
    {
        $this->commentService->delete($comment);

        return back();
    }
}
