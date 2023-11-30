<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registeration\RegisterationRequest;
use App\Models\disease;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterationService extends Controller
{
    public function login(RegisterationRequest $request)
    {
        $User = User::where('code_verfiy', $request->code_verfiy)
            ->where('email', $request->email)
            ->exists();
        if ($User) {
            $UserData = User::where('code_verfiy', $request->code_verfiy)
                ->where('email', $request->email)
                ->first();

            $remember_token = $UserData->createToken('anas')->plainTextToken;
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

        return response()->json($arrJson, 200);
    }
}
