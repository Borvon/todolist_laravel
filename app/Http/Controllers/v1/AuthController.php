<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\dtos\v1\User\AuthUserDto;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request, AuthService $authService)
    {
        $validated = $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $userDto = new AuthUserDto(
            login: $validated['login'],
            password: $validated['password']
        );

        $responseDto = $authService->register($userDto);

        return response()->json($responseDto);
    }

    public function login(Request $request, AuthService $authService)
    {
        $validated = $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $userDto = new AuthUserDto(
            login: $validated['login'],
            password: $validated['password']
        );

        $token = $authService->login($userDto);

        return $this->respondWithToken($token);
    }

    public function me(Request $request, AuthService $authService)
    {
        if (Auth::check())
        {
            $userDto = $authService->me();
            return response()->json($userDto);
        }
        else
        {
            return response()->json(['message' => 'Not authenticated'], 401);
        }
        
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
