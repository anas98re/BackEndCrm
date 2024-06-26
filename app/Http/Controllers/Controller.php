<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message
        ];
        return response()->json($response, 200);
    }

    public function sendSucssas($result, $count = 0)
    {
        $response = [
            'result' => 'success',
            'code' => 200,
            'message' => $result,
            'count' => $count,
        ];
        return response()->json($response, 200);
    }

    public function Unauthenticated($result)
    {
        $response = [
            'result' => 'success',
            'code' => 200,
            'message' => $result
        ];
        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessage = [], $code = 200)
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

    public function TicketResponse($result)
    {
        $response = [
            'result' => 'success',
            'code' => 200,
            'message' => $result['ticket']
        ];
        $response['message']['name_enterprise'] = $result['name_enterprise'];
        $response['message']['nameUser'] = $result['nameUser'];
        $response['message']['categories_ticket_fk'] = $result['Categories'];
        $response['message']['subcategories_ticket_fk'] = $result['Subcategories'];

        return response()->json($response, 200);
    }

    public function TicketResponseToGet($result)
    {
        $response = [
            'result' => 'success',
            'code' => 200,
            'message' => $result['ticket']
        ];
        $response['message']['name_enterprise'] = $result['name_enterprise'];
        $response['message']['nameUser'] = $result['nameUser'];
        $response['message']['categories_ticket_fk'] = $result['Categories'];
        $response['message']['subcategories_ticket_fk'] = $result['Subcategories'];
        $response['message']['status'] = $result['status'];

        return response()->json($response, 200);
    }

}




