<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ApiResponseService
{
    /**
     * Standard API response format:
     * {
     *   success: bool,
     *   response: { code: int, meaning: string, message: string },
     *   data?: mixed,
     *   errors?: mixed
     * }
     */

    public static function success($data = null, string $message = 'Success', int $code = 1, int $http = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'response' => [
                'code' => $code,
                'meaning' => 'success',
                'message' => $message,
            ],
        ];

        if (!is_null($data)) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $http);
    }

    public static function error(string $message = 'Something went wrong', int $code = 0, int $http = 400, $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'response' => [
                'code' => $code,
                'meaning' => 'error',
                'message' => $message,
            ],
        ];

        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $http);
    }

    public static function validation($errors, string $message = 'Validation failed', int $code = 42201, int $http = 422): JsonResponse
    {
        return self::error($message, $code, $http, $errors);
    }

    public static function notFound(string $message = 'Not found', int $code = 40401, int $http = 404): JsonResponse
    {
        return self::error($message, $code, $http);
    }

    public static function forbidden(string $message = 'Forbidden', int $code = 40301, int $http = 403): JsonResponse
    {
        return self::error($message, $code, $http);
    }

    public static function serverError(string $message = 'Server error', int $code = 50001, int $http = 500): JsonResponse
    {
        return self::error($message, $code, $http);
    }
}
