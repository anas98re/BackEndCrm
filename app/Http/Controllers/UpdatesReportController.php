<?php

namespace App\Http\Controllers;

use App\Models\updatesReport;
use App\Http\Requests\StoreupdatesReportRequest;
use App\Http\Requests\UpdateupdatesReportRequest;
use App\Jobs\StorageUpdates;
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
        $this->routePattern = $request->route()->uri();
        $this->ip = $request->ip();
        $this->userName = auth('sanctum')->user()->nameUser;
    }

    public function addUserUpdateReport(Request $request)
    {
        info('request->all() for addUserUpdateReport:', $request->all());
        $modelId = $request->input('id_user');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $userId = $request->input('fk_user_update');

        $description = "User updated by $this->userName, using route: $this->routePattern from IP: $this->ip.";
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
