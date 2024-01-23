<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registeration\RegisterationRequest;
use App\Models\disease;
use App\Models\User;
use App\Models\user_token;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterationService extends Controller
{
    public function login(RegisterationRequest $request)
    {
        $User = users::where('code_verfiy', $request->otp)
            ->where('email', $request->email)
            ->exists();
        if ($User) {
            $UserData = users::where('code_verfiy', $request->otp)
                ->where('email', $request->email)
                ->first();

            $remember_token = $UserData->createToken('anas')->plainTextToken;
            // $fcm_token = $UserData->createToken('anas')->plainTextToken;

            $user_token = new user_token();
            $user_token->token = $request->token;
            $user_token->fkuser = $UserData->id_user;
            $user_token->date_create = Carbon::now('Asia/Riyadh');
            $user_token->save();
        } else {
            return $this->sendUnauthenticated(['Error'], 'Unauthenticated');
        }
        $selectArray = array();
        $index = 0;
        array_push($selectArray, $UserData->id_user);
        $sql = "SELECT u.*, c.nameCountry, r.name_regoin, ll.name_level, c.currency, uus.nameUser as nameuserAdd, ms.name_mange, us1.nameUser as nameuserupdate
        FROM users u
        LEFT JOIN country c ON c.id_country = u.fk_country
        LEFT JOIN regoin r ON r.id_regoin = u.fk_regoin
        INNER JOIN level ll ON u.type_level = ll.id_level
        INNER JOIN managements ms ON ms.idmange = u.type_administration
        INNER JOIN users uus ON u.fkuserAdd = uus.id_user
        LEFT JOIN users us1 ON us1.id_user = u.fkuserupdate
        WHERE u.id_user = ?";

        $result = DB::select($sql, $selectArray);

        $arrJson = array();
        if (count($result) > 0) {
            foreach ($result as $row) {
                $arrJson[] = $row;
                $type_level = $row->type_level;

                $getArray = array();
                array_push($getArray, $type_level);

                $sql1 = "SELECT pruser.*, pr.name_privilege, pr.type_prv, pr.periorty
                    FROM privg_level_user pruser
                    INNER JOIN privileges pr ON pr.id_privilege = pruser.fk_privileg
                    WHERE fk_level = ?
                    ORDER BY pr.periorty ASC";

                $result1 = DB::select($sql1, $getArray);

                if (count($result1) > 0) {
                    $arrJsonProduct = array();
                    foreach ($result1 as $row1) {
                        $arrJsonProduct[] = $row1;
                    }
                    $arrJson[$index]->privilgelist = $arrJsonProduct;
                }
                $index++;
            }
        }
        $arrJson[] = $remember_token;
        // $arrJson[] = $request->token;

        return response()->json($arrJson, 200);
    }

    public function getUsersByTypeAdministrationAndRegion($request)
    {
        $users = users::query()->with(
            'managements:idmange,name_mange',
            'regions:name_regoin,id_regoin'
        )
            ->select(
                'id_user',
                'type_administration',
                'fk_regoin',
                'nameUser'
            );

        $filters = [
            'type_administration' => ['type_administration', 'in'],
            'fk_regoin' => ['fk_regoin', 'in'],
        ];

        foreach ($filters as $key => $filter) {
            if ($request->has($key) && !empty($request->input($key))) {
                $value = $request->input($key);
                $column = $filter[0];
                $operator = $filter[1];

                if (is_array($value)) {
                    $users->whereIn($column, $value);
                } else {
                    $values = explode(',', $value);
                    $users->whereIn($column, $values);
                }
            }
        }


        return $users->get();
    }
}
