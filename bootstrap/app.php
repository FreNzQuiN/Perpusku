<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $renderSafeError = function ($request, string $message, int $status) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $status);
            }

            return response()->view('errors.generic', [
                'message' => $message,
                'status' => $status,
            ], $status);
        };

        $exceptions->render(function (AuthenticationException $e, $request) use ($renderSafeError) {
            return $renderSafeError($request, 'Unauthenticated', 401);
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) use ($renderSafeError) {
            return $renderSafeError($request, 'Resource not found', 404);
        });

        $exceptions->render(function (QueryException $e, $request) use ($renderSafeError) {
            return $renderSafeError($request, 'Terjadi kesalahan pada sistem. Silakan coba lagi nanti.', 500);
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if (!($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], $e->status);
        });

        $exceptions->render(function (\Throwable $e, $request) use ($renderSafeError) {
            if (
                $e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof AuthenticationException ||
                $e instanceof NotFoundHttpException ||
                $e instanceof QueryException
            ) {
                return null;
            }

            return $renderSafeError($request, 'Terjadi kesalahan pada sistem. Silakan coba lagi nanti.', 500);
        });
    })->create();
