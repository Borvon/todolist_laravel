<?php

namespace App\Services;

use App\Http\dtos\v1\Task\TasksPublicDto;
use App\Http\dtos\v1\Task\CreateTaskDto;
use App\Http\dtos\v1\Task\UpdateTaskDto;
use App\Http\dtos\v1\Task\TaskPublicDto;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class TaskService
{
    public function index(int $limit, int $offset)
    {
        if (!$user = Auth::user())
        {
            throw new AuthorizationException();
        }

        $tasks = $user->tasks()->skip($offset)->take($limit)->get();
        $tasksDto = TasksPublicDto::fromCollection($tasks);

        return $tasksDto;
    }

    public function show($id)
    {
        if (!$user = Auth::user())
        {
            throw new AuthorizationException();
        }

        if (!$task = Task::find($id))
        {
            throw new NotFoundHttpException();
        }

        if ($task->user_id != $user->id)
        {
            throw new AuthorizationException();
        }

        $taskDto = TaskPublicDto::fromModel($task);

        return $taskDto;
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

    public function update($id, UpdateTaskDto $updateDto)
    {
        if (!$user = Auth::user())
        {
            throw new AuthorizationException();
        }

        if (!$task = Task::find($id))
        {
            throw new NotFoundHttpException();
        }

        if ($task->user_id != $user->id)
        {
            throw new AuthorizationException();
        }

        if (!is_null($updateDto->title)) $task->title = $updateDto->title;
        if (!is_null($updateDto->description)) $task->description = $updateDto->description;
        if (!is_null($updateDto->due_date)) $task->due_date = $updateDto->due_date;
        if (!is_null($updateDto->status)) $task->status = $updateDto->status;
        $task->save();
        $task->refresh();

        $responseDto = TaskPublicDto::fromModel($task);
        return $responseDto;
    }

    public function delete()
    {

    }
}
