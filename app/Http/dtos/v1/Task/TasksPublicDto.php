<?php

namespace App\Http\dtos\v1\Task;

use App\Models\Task;
use Illuminate\Support\Collection;
use App\Http\dtos\v1\Task\TasksPublicDto;

class TasksPublicDto
{
    public Collection $tasks;

    public function __construct(Collection $tasks)
    {
        $this->tasks = $tasks;
    }

    public static function fromCollection(Collection $collection)
    {
        $tasks = collect($collection)->map(function ($task) {
            return TaskPublicDto::fromModel($task);
        });

        return new self($tasks);
    }
}