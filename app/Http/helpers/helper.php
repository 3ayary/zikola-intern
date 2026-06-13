<?php

namespace App\Http\helpers;


function ApiResponse($data, string $message = 'success', int $status = 200)
{
    return response()->json([
        'message' => $message,
        'data'    => $data,
    ], $status);
}
