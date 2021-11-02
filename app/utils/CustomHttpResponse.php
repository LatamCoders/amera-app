<?php

namespace App\utils;

class CustomHttpResponse
{
    public static function HttpReponse($message, $data, $status): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => $message, 'data' => $data, 'status' => $status], $status);
    }
}
