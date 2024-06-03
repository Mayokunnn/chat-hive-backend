<?php

namespace App\Traits;

trait Response
{
    protected function error($message, $error = [] ,$statusCode){
        return response()->json([
            'message' => $message,
            'error' => $error,
            'status' => $statusCode,
        ], $statusCode);
    }

    protected function success($message, $data= [], $statusCode= 200){
        return response()->json([
            'message' => $message,
            'data' => $data,
            'status' => $statusCode
        ], $statusCode);
    }
}