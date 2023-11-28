<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function checkEmail(Request $request)
    {
        $verificationCode = Str::random(6);
        Mail::to($request->email)->send(new VerificationCodeEmail($verificationCode));
        return response()->json(['success']);
    }

    public function login()
    {
        
    }


    public function register(Request $request)
    {
        $input = $request->all();
        $input['type_level'] = 1;
        $user = User::create($input);
        $success['remember_token'] = $user->createToken('anas')->plainTextToken;
        $success['nameUser'] = $user->nameUser;
        $success['id_user'] = $user->id_user;
        $success['email'] = $user->email;
        return response()->json([$success]);
    }
}
