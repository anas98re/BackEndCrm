<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function sendResponse($result, $message){ // fun sends theResponse to user with result and massage
        $response = [
         'success' => true,
         'data' => $result, //the date that comes from the database is stored  in result variable
         'message' =>$message
         //succes is Key and true is  value
        ] ;


        return response()->json( $response, 200); // 200 is http protocol // send data to user as json

     }

     public function sendError($error, $errorMessage=[], $code = 404){
         $response = [


          'success' => false,
         //  'data' => $error,
          'message' =>$error

         ];
         if(!empty($errorMessage))
         {
             $response['data']=$errorMessage;
         }
         return response()->json($response, $code);
 }

}
