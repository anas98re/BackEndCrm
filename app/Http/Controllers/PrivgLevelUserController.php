<?php

namespace App\Http\Controllers;

use App\Models\privg_level_user;
use App\Http\Requests\Storeprivg_level_userRequest;
use App\Http\Requests\Updateprivg_level_userRequest;
use App\Mail\sendupdatePermissionsReportToEmail;
use App\Models\level;
use App\Models\levelModel;
use App\Models\privilageReport;
use App\Models\privileges;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PrivgLevelUserController extends Controller
{
    // public function updatePermissions1(Request $request)
    // {
    //     $updatedData = [];
    //     $data = $request->all();
    //     for ($i = 0; $i < count($request->id_privg_user); $i++) {
    //         DB::table('privg_level_user')
    //             ->where('id_privg_user', $data['id_privg_user'][$i])
    //             ->update(['is_check' => $data['is_check'][$i]]);
    //         $updatedData[] = DB::table('privg_level_user')
    //             ->where('id_privg_user', $data['id_privg_user'][$i])->first();
    //     }
    //     // return $updatedData;
    //     $levelName = null;
    //     foreach ($updatedData as $key => $value) {
    //         $id[] = $value->fk_privileg;
    //         $name = privileges::where('id_privilege', $value->fk_privileg)
    //             ->first()
    //             ->name_privilege;
    //         $onOrOff = ($value->is_check == 1 ? 'ON' : 'OFF');
    //         $nameAndCheck[] = $name . '(' . $onOrOff . ')';
    //         $levelName = levelModel::where('id_level', $value->fk_level)
    //             ->first()->name_level;
    //     }
    //     $messageNameAndCheck = implode("\n", $nameAndCheck);

    //     $userName = null;
    //     $userId = null;
    //     if ($request->has('fk_user')) {
    //         $userName = users::where('id_user', $request->fk_user)->first()->nameUser;
    //         $userId = users::where('id_user', $request->fk_user)->first()->id_user;
    //     }

    //     $privilageReport = new privilageReport();
    //     $privilageReport->changes_data = $messageNameAndCheck;
    //     $privilageReport->level_name = $levelName;
    //     $privilageReport->edit_date = Carbon::now('Asia/Riyadh')->toDateTimeString();;
    //     $privilageReport->user_update_name = $userName;
    //     $privilageReport->fkuser = $userId;
    //     $privilageReport->save();

    //     return $this->sendResponse($updatedData, 'Updated success');
    // }

    public function updatePermissions(Request $request)
    {
        $updatedData = [];
        $data = $request->all();

        for ($i = 0; $i < count($request->id_privg_user); $i++) {
            $privgLevelUser = privg_level_user::where(
                'id_privg_user',
                $data['id_privg_user'][$i]
            )->first();
            $privgLevelUser->is_check = $data['is_check'][$i];
            $privgLevelUser->save();

            $updatedData[] = $privgLevelUser;
        }

        $levelName = null;
        foreach ($updatedData as $key => $value) {
            $id[] = $value->fk_privileg;
            $name = privileges::where('id_privilege', $value->fk_privileg)->first()->name_privilege;
            $onOrOff = ($value->is_check == 1 ? 'ON' : 'OFF');
            $nameAndCheck[] = $name . '(' . $onOrOff . ')';
            $levelName = levelModel::where('id_level', $value->fk_level)->first()->name_level;
        }
        $messageNameAndCheck = implode("\n", $nameAndCheck);

        $userName = null;
        $userId = null;
        if ($request->has('fk_user')) {
            $userName = users::where('id_user', $request->fk_user)->first()->nameUser;
            $userId = users::where('id_user', $request->fk_user)->first()->id_user;
        }

        $privilageReport = new privilageReport();
        $privilageReport->changes_data = $messageNameAndCheck;
        $privilageReport->level_name = $levelName;
        $privilageReport->edit_date = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $privilageReport->user_update_name = $userName;
        $privilageReport->fkuser = $userId;
        $privilageReport->save();

        return $this->sendResponse($updatedData, 'Updated success');
    }

    public function sendupdatePermissionsReportToEmail(Request $request)
    {
        // $today = Carbon::today()->toDateString();
        // $privilageReport = privilageReport::whereDate('edit_date', $today)->get();

        $yesterday = Carbon::yesterday()->startOfDay();
        $now = Carbon::now('Asia/Riyadh');

        $privilageReport = privilageReport::with('user')
            ->whereBetween('edit_date', [$yesterday, $now])
            ->get();
        if (count($privilageReport) > 0) {
            Mail::to($request->email)->send(new sendupdatePermissionsReportToEmail($privilageReport));
            return $this->sendResponse('true', 'done');
        } else {
            return $this->sendResponse('error', 'لا يوجد صلاحيات تم تعديلها من الامس حتى هذه اللحظة');
        }
    }


    public function insertPrivelgeToAllLevel(Request $request)
    {
        try {
            DB::beginTransaction();

            // Fetch the last ID from the table
            $lastId = privileges::orderBy('id_privilege', 'desc')->value('id_privilege');

            // Increment the last ID and use it for the new record
            $id_privilege = $lastId + 1;

            $requestData = $request->all();
            $requestData['id_privilege'] = $id_privilege;

            privileges::insert($requestData);

            $levels = levelModel::all();

            $allowedLevels = ['المالك', 'مدير مبيعات', 'المسؤولين'];

            foreach ($levels as $level) {
                if (in_array($level->name_level, $allowedLevels)) {
                    privg_level_user::insert([
                        'fk_level' => $level->id_level,
                        'fk_privileg' => $id_privilege,
                        'is_check' => 1
                    ]);
                } else {
                    privg_level_user::insert([
                        'fk_level' => $level->id_level,
                        'fk_privileg' => $id_privilege,
                        'is_check' => 0
                    ]);
                }
            }

            DB::commit();
            return $this->sendResponse(true, 'Added success');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }
}
