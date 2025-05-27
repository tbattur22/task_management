<?php

namespace App\Services;

use App\Models\Task;

// app/Services/TaskService.php
class TaskService {
    public function create(array $data): Task {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task {
        $task->update($data);
        return $task;
    }
}
