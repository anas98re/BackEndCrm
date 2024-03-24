<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // public $currectUserId;

    // public function __construct()
    // {
    //     $this->currectUserId = auth('sanctum')->user()->id_user;
    // }

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message
        ];
        return response()->json($response, 200);
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

    public function allTicketResponse($result)
    {
        $tickets = [];

        foreach ($result as $ticket) {
            $ticketData = [
                'ticket' => $ticket['ticket'],
                'name_enterprise' => $ticket['name_enterprise'],
                'nameUser' => $ticket['nameUser'],
                'categories_ticket_fk' => $ticket['Categories'],
                'subcategories_ticket_fk' => $ticket['Subcategories'],
            ];

            $tickets[] = $ticketData;
        }

        $response = [
            'result' => 'success',
            'code' => 200,
            'message' => $tickets
        ];

        return response()->json($response, 200);
    }
}
