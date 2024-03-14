<?php

namespace App\Http\Controllers;

use App\Models\updatesReport;
use App\Http\Requests\StoreupdatesReportRequest;
use App\Http\Requests\UpdateupdatesReportRequest;
use App\Jobs\StorageUpdates;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UpdatesReportController extends Controller
{
    protected $routePattern;
    protected $userName;
    protected $ip;

    public function __construct()
    {
        $request = app(Request::class);
        // $this->routePattern = $request->route()->uri();
        $this->ip = $request->ip();
        // $this->userName = auth('sanctum')->user()->nameUser;
    }

    public function addUserUpdateReport(Request $request)
    {
        info('request->all() for addUserUpdateReport:', $request->all());

        $dataAfterUpdateForMainCity = json_decode($request->input('dataAfterUpdateForMainCity'), true)[0];
        $dataBeforeUpdateForMainCity = json_decode($request->input('dataBeforeUpdateForMainCity'), true)[0];
        info('dataAfterUpdateForMainCity ', array($dataAfterUpdateForMainCity));
        info('dataBeforeUpdateForMainCity ', array($dataBeforeUpdateForMainCity));
        $differencesMainCity = array_diff_assoc($dataAfterUpdateForMainCity, $dataBeforeUpdateForMainCity);

        info('$differences for MainCity: ', array($differencesMainCity));

        $modelId = $request->input('id_user');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $userId = $request->input('fk_user_update');

        $userName = users::where('id_user', $userId)->first()->nameUser;
        $routePattern = 'updateuser_patch.php';
        $description = "User updated by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = 'تعديل بيانات المستخدم ';
        $model = 'users';

        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $description
        );
    }
}
