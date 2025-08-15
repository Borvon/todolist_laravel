<?php

namespace App\Services;

use App\Http\dtos\v1\Task\CreateTaskDto;
use App\Http\dtos\v1\Task\TaskPublicDto;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class TaskService
{
    public function index()
    {

    }

    public function show()
    {

    }

    public function store(CreateTaskDto $taskDto)
    {
        if (!$user = Auth::user())
        {
            throw new AuthorizationException();
        }

        $task = new Task;
        $task->title = $taskDto->title;
        if (!is_null($taskDto->description)) $task->description = $taskDto->description;
        if (!is_null($taskDto->due_date)) $task->due_date = $taskDto->due_date;
        if (!is_null($taskDto->status)) $task->status = $taskDto->status;
        $task->user_id = $user->id;
        $task->save();
        $task->refresh();

        $responseDto = TaskPublicDto::fromModel($task);

        return $responseDto;
    }

    public function update()
    {

    }

    public function delete()
    {

    }
}
