<?php

namespace App\Services;

class JsonResponeService
{
    public $currectUserId;

    public function __construct()
    {
        $this->currectUserId = auth('sanctum')->user()->id_user;
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message
        ];
        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessage = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error
        ];
        if (!empty($errorMessage)) {
            $response['data'] = $errorMessage;
        }
        return response()->json($response, $code);
    }

    public function sendSucssas($result)
        {
            $response = [
                'result' => 'success',
                'code' => 200,
                'message' => $result
            ];
            return response()->json($response, 200);
        }

    public function sendUnauthenticated($error, $errorMessage = [], $code = 401)
    {
        $response = [
            'success' => false,
            'message' => $error
        ];
        if (!empty($errorMessage)) {
            $response['data'] = $errorMessage;
        }
        return response()->json($response, $code);
    }
}
