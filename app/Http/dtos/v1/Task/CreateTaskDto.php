<?php

namespace App\Http\dtos\v1\Task;

class CreateTaskDto
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $due_date,
        public readonly ?string $status,
        ) {}
}