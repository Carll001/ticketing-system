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
            'content' =>   Auth::user()->name . ' created a task named: ' . $data['title'] ,
        ]);

        // 3. Return the TASK object so the controller can redirect correctly
        return $task;
    }



    public function update($task, array $data)
    {
        $task->update($data);


        Transaction::create([
            'content' => Auth::user()->name . ' updated a task named: ' . $data['title'] ,
        ]);

        return $task;
    }
}
