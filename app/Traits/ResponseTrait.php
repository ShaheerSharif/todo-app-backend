<?php

namespace App\Traits;

trait ResponseTrait
{
    protected function successResponse(?array $data, int $statusCode = 200, string $message = 'Success')
    {
        $data = $data !== null ? $data : [];

        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $statusCode,
            'data' => $data,
        ]);
    }

    protected function errorResponse(?array $data, int $statusCode, string $message = 'Error')
    {
        $data = $data !== null ? $data : [];

        return response()->json([
            'success' => false,
            'message' => $message,
            'status' => $statusCode,
            'data' => $data,
        ]);
    }
}
