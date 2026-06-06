<?php

namespace App\Http\Responses;

class ApiResponse
{
    public static function success($data, string $message = 'success', int $status = 200)
    {
        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], $status);
    }
    public static function error(string $message = 'error', int $status = 400)
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }
}
