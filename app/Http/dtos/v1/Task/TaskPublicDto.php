<?php

namespace App\Http\dtos\v1\Task;

use App\Models\Task;

class TaskPublicDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $due_date,
        public readonly string $status,
        public readonly int $user_id,
        public readonly string $created_at,
        public readonly string $updated_at
        ) {}

    public static function fromModel(Task $task)
    {
        return new self(
        id: $task->id,
        title: $task->title,
        description: $task->description,
        due_date: $task->due_date,
        status: $task->status,
        user_id: $task->user_id,
        created_at: $task->created_at,
        updated_at: $task->updated_at
        );
    }
}