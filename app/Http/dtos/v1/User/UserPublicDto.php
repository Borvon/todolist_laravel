<?php

namespace App\Http\dtos\v1\User;

class UserPublicDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $login,
        public readonly string $created_at,
        public readonly string $updated_at,
        ) {}

}