<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Throwable;

class ApiErrorResponse
{
    public static function make(
        string $message,
        int $status,
        array $errors = [],
        ?Throwable $exception = null
    ): JsonResponse {
        $payload = [
            "success" => false,
            "message" => $message,
            "status" => $status,
        ];

        if ($errors !== []) {
            $payload["errors"] = $errors;
        }

        if (config("app.debug") && $exception) {
            $payload["debug"] = [
                "exception" => $exception::class,
                "message" => $exception->getMessage(),
                "file" => $exception->getFile(),
                "line" => $exception->getLine(),
            ];
        }

        return response()->json($payload, $status);
    }
}
