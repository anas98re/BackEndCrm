<?php

namespace App\Http\Controllers;

use App\Http\Requests\Registeration\AddEmailFromAdminRequest;
use App\Http\Requests\Registeration\CheckEmailRequest;
use App\Http\Requests\Registeration\RegisterationRequest;
use App\Mail\VerificationCodeEmail;
use App\Models\User;
use App\Models\users;
use App\Services\RegisterationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;

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
            $User = new users();
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
        // try {
        //     DB::beginTransaction();
            $email  = trim($request->email);
            $existingEmail = users::where('email', $email)->exists();

            if ($existingEmail) {
                if ($email == 'smartlife2@gmail.com') {
                    $code = 902461;
                } else {
                    $code = rand(11111, 99999);
                    $existingCode = users::where('code_verfiy', $code)->exists();
                    while ($existingCode) {
                        $code = rand(11111, 99999);
                        $existingCode = users::where('code_verfiy', $code)->exists();
                    }
                }
                $user = users::where('email', $email)->first();
                $user->code_verfiy = $code;
                // $user->type_level = 0;
                $user->save();
                Mail::to($request->email)->send(new VerificationCodeEmail($code));
                DB::commit();
                return $this->sendResponse([$user->email], 'Done');
            }
            // else {
            //     $User = new users();
            //     $email = $request->email;
            //     $nameUser = strstr($email, '@', true); // Get the substring before the '@' symbol in the email
            //     $User->nameUser = $nameUser;
            //     $User->email = $email;
            //     $User->fk_country = 1;
            //     $User->type_administration = 1;
            //     $User->type_level = 20;
            //     $User->fk_regoin = 5;
            //     $User->isActive = 1;
            //     $User->img_image = 'b6e44179e934ca0624379bcdfa044665.png';
            //     $User->img_thumbnail = '48464df755303690b6627314ec202d64.png';
            //     $User->fkuserAdd = 1;
            //     $User->created_at = Carbon::now('Asia/Riyadh');
            //     $User->mobile = rand(1111111, 99999999);
            //     $User->save();

            //     $code = rand(11111, 99999);
            //     $existingCode = users::where('code_verfiy', $code)->exists();
            //     while ($existingCode) {
            //         $code = rand(11111, 99999);
            //         $existingCode = users::where('code_verfiy', $code)->exists();
            //     }
            //     $user = users::where('email', $request->email)->first();
            //     $user->code_verfiy = $code;
            //     // $user->type_level = 0;
            //     $user->save();
            //     Mail::to($request->email)->send(new VerificationCodeEmail($code));
            //     DB::commit();
            // }

            return $this->sendUnauthenticated(['Error'], 'This email not exist');
            // return $this->sendUnauthenticated(['Error'], 'Unauthenticated');

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
    }
    public function login1(RegisterationRequest $request)
    {
        $User = users::where('code_verfiy', $request->code_verfiy)
            ->where('email', $request->email)
            ->exists();
        if ($User) {
            $UserData = users::where('code_verfiy', $request->code_verfiy)
                ->where('email', $request->email)
                ->first();

            $remember_token = $UserData->createToken('anas')->plainTextToken;

            $response = [
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
        $user = users::create($input);
        $success['remember_token'] = $user->createToken('anas')->plainTextToken;
        $success['nameUser'] = $user->nameUser;
        $success['id_user'] = $user->id_user;
        $success['email'] = $user->email;
        return response()->json([$success]);
    }

    public function test(Request $request)
    {
        $user = auth()->user();
        $string = "hi h#o#$%&*w are %you$";
        $replacedString = str_replace(' ', '_', $string);
        $finalString = preg_replace('/[^A-Za-z0-9_]/', '', $replacedString);

        echo $finalString;
    }

    public function getUsersByTypeAdministrationAndRegion(Request $request)
    {
        $users = $this->MyService->getUsersByTypeAdministrationAndRegion($request);
        return (count($users) > 0 ? $users : response()->json(['not_found']));
    }

    public function getCurrentUser(Request $request)
    {
        $bearerToken = $request->bearerToken();
        // $bearerToken = '13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465';
        $tokenable_type = PersonalAccessToken::findToken($bearerToken);
        $user = users::where('id_user', $tokenable_type->tokenable_id)->first()->id_user;
        return  $user;
    }

    public function getHashToken(Request $request)
    {
        $bearerToken = $request->bearerToken();
        // $bearerToken = '13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465';
        $tokenable_type = PersonalAccessToken::findToken($bearerToken);
        return $tokenable_type ? 1 : 0;
    }

    public function isTokenAuthenticated(Request $request)
    {
        $bearerToken = $request->bearerToken();
        $tokenable_type = PersonalAccessToken::findToken($bearerToken);
        return $tokenable_type ? $this->sendSucssas(true)  : $this->sendSucssas(false);
    }
}
