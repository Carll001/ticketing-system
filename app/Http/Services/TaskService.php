<?php

namespace App\Http\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskService
{

    public function store(array $data)
    {
        return Task::create([
            ...$data,
            'creator_id' => Auth::id(),
        ]);
    }


    public function update($task, array $data)
    {
        
        $task->update($data);
        
        return $task;
    }

}
?>