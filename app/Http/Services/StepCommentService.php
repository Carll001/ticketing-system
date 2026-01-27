<?php

namespace App\Http\Services;

use App\Models\Step;
use App\Models\StepComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class StepCommentService
{
    /**
     * Create a comment for a step
     *
     * @param Step $step
     * @param array $data
     * @return StepComment
     */
    public function store(Step $step, array $data): StepComment
    {
        return StepComment::create([
            'content' => $data['content'],
            'step_id' => $step->id,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Delete a comment
     *
     * @param StepComment $comment
     * @return void
     */
    public function delete(StepComment $comment): void
    {
        // Optional safety check: only owner can delete
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();
    }
}
