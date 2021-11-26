<?php

namespace App\utils;

use Illuminate\Http\JsonResponse;

class CustomHttpResponse
{
    public static function HttpReponse($message, $data, $status): JsonResponse
    {
        return response()->json(['message' => $message, 'data' => $data, 'status' => $status], $status);
    }
}
