<?php

namespace App\Traits;

class ResponseService{
    public static function error($message, $error = [] ,$statusCode){
        return response()->json([
            'message' => $message,
            'error' => $error,
            'status' => $statusCode,
        ], $statusCode);
    }

    public static function success($message, $data= [], $statusCode= 200){
        return response()->json([
            'message' => $message,
            'data' => $data,
            'status' => $statusCode
        ], $statusCode);
    }
}