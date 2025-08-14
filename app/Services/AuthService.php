<?php

namespace App\Services;

use App\Http\dtos\v1\User\AuthUserDto;
use App\Http\dtos\v1\User\UserPublicDto;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;

class AuthService
{
    public function register(AuthUserDto $userDto)
    {
        $user = new User;
        $user->login = $userDto->login;
        $user->password = Hash::make($userDto->password);
        $user->save();
        
        $responseDto = new UserPublicDto(
            id: $user->id,
            login: $user->login,
            created_at: $user->created_at,
            updated_at: $user->updated_at
        );

        return $responseDto;
    }

    public function login(AuthUserDto $userDto)
    {
        $credentials = [
            'login' => $userDto->login,
            'password' => $userDto->password
        ];

        if (!$token = JWTAuth::attempt($credentials))
        {
            throw new AuthenticationException('Invalid credentials');
        }

        return $token;
    }

    public function me()
    {
        $user = Auth::user();

        $userDto = new UserPublicDto(
            id: $user->id,
            login: $user->login,
            created_at: $user->created_at,
            updated_at: $user->updated_at
        );

        return $userDto;
    }
}
