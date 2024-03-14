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

        $dataAfterUpdateForMainCity = json_decode($request->input('dataAfterUpdateForMainCity'), true);


        $nameMainCitiesAfter = [];
        if ($dataAfterUpdateForMainCity) {
            foreach ($dataAfterUpdateForMainCity as $item) {
                $namemaincity = $item['namemaincity'];
                $nameMainCitiesAfter[] = $namemaincity;
            }
        }

        $dataBeforeUpdateForMainCity = json_decode($request->input('dataBeforeUpdateForMainCity'), true);

        $nameMainCitiesBefor = [];
        if ($dataBeforeUpdateForMainCity) {
            foreach ($dataBeforeUpdateForMainCity as $item) {
                $namemaincity = $item['namemaincity'];
                $nameMainCitiesBefor[] = $namemaincity;
            }
        }

        info('dataAfterUpdateForMainCity ', array($nameMainCitiesAfter));
        info('dataBeforeUpdateForMainCity ', array($nameMainCitiesBefor));


        $modelId = $request->input('id_user');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];

        $dataBeforeUpdate['nameMainCitiesBefore'] = $nameMainCitiesBefor;
        $dataAfterUpdate['nameMainCitiesAfter'] = $nameMainCitiesAfter;

        info('$dataBeforeUpdate[nameMainCitiesBefore] ', array($dataBeforeUpdate));
        info('$dataAfterUpdate[nameMainCitiesAfter] ', array($dataAfterUpdate));

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
