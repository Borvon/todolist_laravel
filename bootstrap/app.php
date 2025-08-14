<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (UniqueConstraintViolationException $e)
        {
            if ($e->getCode() === '23000')
            {
                return response()->json(['message' => 'Integrity constraint violation'], 422);
            }
        });
        $exceptions->renderable(function (AuthenticationException $e)
        {
            return response()->json(['message' => 'Not authenticated'], 401);
        });
        
    })->create();
