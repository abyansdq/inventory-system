<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verified.active'    => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Handle 403 dari Spatie Permission
        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses ke halaman ini.',
                ], 403);
            }

            return response()->view('errors.403', [], 403);
        });

        // Handle 404
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e,
            Request $request
        ) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Resource tidak ditemukan.'], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // Handle 500
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (app()->environment('production') && !$request->expectsJson()) {
                return response()->view('errors.500', [], 500);
            }
        });

    })->create();