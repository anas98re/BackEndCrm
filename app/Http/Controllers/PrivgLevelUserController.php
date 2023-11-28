<?php

namespace App\Http\Controllers;

use App\Models\privg_level_user;
use App\Http\Requests\Storeprivg_level_userRequest;
use App\Http\Requests\Updateprivg_level_userRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrivgLevelUserController extends Controller
{
    public function updatePermissions(Request $request)
    {
        $updatedData = [];
        return $data = $request->all();
        for ($i = 0; $i < count($data); $i++) {
            DB::table('privg_level_user')
                ->where('id_privg_user', $data['id_privg_user'][$i])
                ->update(['is_check' => $data['is_check'][$i]]);
            $updatedData[] = DB::table('privg_level_user')
                ->where('id_privg_user', $data['id_privg_user'][$i])->first();
        }

        return $this->sendResponse($updatedData,'Updated success');
    }
}
