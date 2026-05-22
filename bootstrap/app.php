<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Support\ApiErrorResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => null);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            $errors = $exception->errors();
            $firstField = array_key_first($errors);
            $firstMessage = $errors[$firstField][0] ?? 'Los datos enviados no son válidos.';

            return ApiErrorResponse::make(
                message: $firstMessage,
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
                errors: $errors
            );
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            return ApiErrorResponse::make(
                message: match ($exception->getMessage()) {
                    '', 'Unauthenticated.' => 'No autenticado.',
                    default => $exception->getMessage(),
                },
                status: Response::HTTP_UNAUTHORIZED
            );
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            return ApiErrorResponse::make(
                message: $exception->getMessage() ?: 'No tienes permisos para realizar esta acción.',
                status: Response::HTTP_FORBIDDEN
            );
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            return ApiErrorResponse::make(
                message: 'El recurso solicitado no existe.',
                status: Response::HTTP_NOT_FOUND
            );
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            return ApiErrorResponse::make(
                message: 'La ruta solicitada no existe.',
                status: Response::HTTP_NOT_FOUND
            );
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, Request $request) {
            return ApiErrorResponse::make(
                message: 'El método HTTP no está permitido para esta ruta.',
                status: Response::HTTP_METHOD_NOT_ALLOWED
            );
        });

        $exceptions->render(function (UnauthorizedHttpException $exception, Request $request) {
            return ApiErrorResponse::make(
                message: match ($exception->getMessage()) {
                    'Token not provided', 'A token is required', 'The token could not be parsed from the request', 'Token could not be parsed from the request.' => 'Token no proporcionado.',
                    'Token has expired' => 'Token expirado.',
                    'User not found' => 'Usuario no encontrado para este token.',
                    default => 'Token inválido.',
                },
                status: Response::HTTP_UNAUTHORIZED,
                exception: $exception
            );
        });

        $exceptions->render(function (QueryException $exception, Request $request) {
            $sql = $exception->getMessage();
            $code = $exception->getCode();

            // MySQL: 1451 (FK), 1062 (Unique) | SQLite: 19 (FK/Unique) | PostgreSQL: 23503 (FK), 23505 (Unique)
            if (str_contains($sql, '1451') || str_contains($sql, 'FOREIGN KEY constraint failed') || str_contains($sql, '23503')) {
                $message = 'No se puede eliminar el registro porque está relacionado con otros datos.';
            } elseif (str_contains($sql, '1062') || str_contains($sql, 'UNIQUE constraint failed') || str_contains($sql, '23505')) {
                $message = 'El valor ingresado ya existe en el sistema.';
            } else {
                $message = 'No se pudo procesar la operación en la base de datos.';
            }

            return ApiErrorResponse::make(
                message: $message,
                status: Response::HTTP_CONFLICT,
                exception: $exception
            );
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            return ApiErrorResponse::make(
                message: $exception->getMessage()
                    ?: (Response::$statusTexts[$exception->getStatusCode()] ?? 'Error HTTP.'),
                status: $exception->getStatusCode(),
                exception: $exception
            );
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            return ApiErrorResponse::make(
                message: 'Ocurrió un error inesperado.',
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                exception: $exception
            );
        });
    })->create();
