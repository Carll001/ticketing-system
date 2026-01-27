<?php

namespace App\Http\Services;

use App\Models\Task;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    /**
     * Store a new task and create a transaction log
     *
     * @param array $data
     * @return Task
     */
    public function store(array $data): Task
    {
        // 1. Create the task
        $task = Task::create([
            ...$data,
            'creator_id' => Auth::id(),
        ]);

        // 2. Log transaction
        Transaction::create([
            'content' => 'Created task',
            'user_id' => Auth::id(),
            'task_id' => $task->id,
        ]);

        // 3. Return the task object
        return $task;
    }

    /**
     * Update an existing task and optionally create a transaction log
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        // Optional: Log transaction for update
        Transaction::create([
            'content' => 'Updated task',
            'user_id' => Auth::id(),
            'task_id' => $task->id,
        ]);

        return $task;
    }

    /**
     * Delete a task and optionally create a transaction log
     *
     * @param Task $task
     * @return void
     */
    public function delete(Task $task): void
    {
        // Optional: Log transaction before deletion
        Transaction::create([
            'content' => 'Deleted task',
            'user_id' => Auth::id(),
            'task_id' => $task->id,
        ]);

        $task->delete();
    }
}
