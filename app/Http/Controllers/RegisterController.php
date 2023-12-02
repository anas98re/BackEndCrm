<?php

namespace App\Http\Controllers;

use App\Http\Requests\Registeration\AddEmailFromAdminRequest;
use App\Http\Requests\Registeration\CheckEmailRequest;
use App\Http\Requests\Registeration\RegisterationRequest;
use App\Mail\VerificationCodeEmail;
use App\Models\User;
use App\Models\users;
use App\Services\RegisterationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{
    private $MyService;

    public function __construct(RegisterationService $MyService)
    {
        $this->MyService = $MyService;
    }
    public function addEmailFromAdmin(AddEmailFromAdminRequest $request)
    {
        try {
            DB::beginTransaction();
            $User = new User();
            $User->nameUser = $request->nameUser;
            $User->email = $request->email;
            $User->mobile = $request->mobile;
            $User->type_level = $request->type_level;
            $User->save();
            DB::commit();
            return response()->json(['message' => 'success', 'data' => $User], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkEmail(CheckEmailRequest $request)
    {
        try {
            DB::beginTransaction();

            $existingEmail = User::where('email', $request->email)->exists();
            if ($existingEmail) {
                $code = Str::random(5);
                $existingCode = User::where('code_verfiy', $code)->exists();
                while ($existingCode) {
                    $code = Str::random(5);
                    $existingCode = User::where('code_verfiy', $code)->exists();
                }
                $user = User::where('email', $request->email)->first();
                $user->code_verfiy = $code;
                $user->type_level = 0;
                $user->save();
                Mail::to($request->email)->send(new VerificationCodeEmail($code));

                DB::commit();
                return $this->sendResponse([$user->email], 'Done');
            }

            return $this->sendUnauthenticated(['Error'], 'Unauthenticated');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function login1(RegisterationRequest $request)
    {
          ->first();

            $remember_token = $UserData->createToken('anas')->plainTextToken;

            $response = [
               $User = User::where('code_verfiy', $request->code_verfiy)
            ->where('email', $request->email)
            ->exists();
        if ($User) {
            $UserData = User::where('code_verfiy', $request->code_verfiy)
                ->where('email', $request->email)
               'data' => $UserData,
                'token' => $remember_token,
                'message' => 'signed in Done'
            ];
            return response()->json($response, 200);
        }
        return $this->sendUnauthenticated(['Error'], 'Unauthenticated');
    }

    public function login(RegisterationRequest $request)
    {
        return $this->MyService->login($request);
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

    public function test(Request $request)
    {
        return $user = auth()->user();
        $string = "hi h#o#$%&*w are %you$";
        $replacedString = str_replace(' ', '_', $string);
        $finalString = preg_replace('/[^A-Za-z0-9_]/', '', $replacedString);

        echo $finalString;
    }
}
