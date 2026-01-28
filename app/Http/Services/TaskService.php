<?php

namespace App\Http\Services;

use App\Models\Task;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TaskService
{

    public function store(array $data)
    {
        // 1. Create the Task
        $task = Task::create([
            ...$data,
            'creator_id' => Auth::id(),
        ]);

        // 2. Create the Transaction (The boot logic above handles the ID and TSK number)
        Transaction::create([
            'content' => 'created task',
            'user_id' => Auth::id(),
            'task_id' => $task->id,
        ]);

        // 3. Return the TASK object so the controller can redirect correctly
        return $task;
    }



    public function update($task, array $data)
    {

        $task->update($data);

        return $task;
    }
}
