<?php

use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\PanelAuth;
use App\Http\Responses\ApiResponse;
use App\Models\ErrorLog;
use App\Support\ApiErrorCodes;
use App\Support\ApiMessages;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt' => JwtMiddleware::class,
            'panel.auth' => PanelAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiResponse::validationError($e->errors());
            }
        });

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiResponse::notFound(ApiMessages::ENDPOINT_NOT_FOUND);
            }
        });

        $exceptions->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiResponse::error(ApiErrorCodes::FORBIDDEN, ApiMessages::METHOD_NOT_ALLOWED, 405);
            }
        });

        $exceptions->renderable(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                if ($e instanceof ValidationException || $e instanceof NotFoundHttpException || $e instanceof MethodNotAllowedHttpException) {
                    return null;
                }

                $errorCode = 'ERR-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

                try {
                    $user = $request->attributes->get('jwt_user');
                    $requestBody = $request->except(['password', 'password_confirmation']);

                    ErrorLog::create([
                        'error_code' => $errorCode,
                        'message' => $e->getMessage(),
                        'stack_trace' => $e->getTraceAsString(),
                        'user_id' => $user?->id,
                        'endpoint' => $request->getPathInfo(),
                        'http_method' => $request->getMethod(),
                        'request_body' => !empty($requestBody) ? json_encode($requestBody) : null,
                        'created_at' => now(),
                    ]);
                } catch (\Throwable $logException) {
                }

                return ApiResponse::error(
                    ApiErrorCodes::INTERNAL_ERROR,
                    "Terjadi kesalahan, silakan hubungi admin [$errorCode]",
                    500
                );
            }
        });
    })->create();
