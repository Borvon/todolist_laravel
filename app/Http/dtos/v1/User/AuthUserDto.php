<?php

namespace App\Http\dtos\v1\User;

class AuthUserDto
{
    public function __construct(
        public readonly string $login,
        public readonly string $password
        ) {}
}