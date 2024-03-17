<?php

namespace App\Http\Controllers;

use App\Models\updatesReport;
use App\Http\Requests\StoreupdatesReportRequest;
use App\Http\Requests\UpdateupdatesReportRequest;
use App\Jobs\StorageClientsUpdatesJob;
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

        $userName = null;
        if ($userId) {
            $userName = users::where('id_user', $userId)->first()->nameUser;
        }
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
            $description,
            $nameMainCitiesBefor
        );
    }

    public function storageClientsUpdates(Request $request)
    {
        $modelId = $request->input('id_client');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $userId = $request->input('fk_idUser');

        $userName = null;
        if ($userId) {
            $userName = users::where('id_user', $userId)->first()->nameUser;
        }

        $routePattern = 'clientUpdate.php';
        $description = "Client updated by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = 'تعديل بيانات العميل ';
        $model = 'clients';

        $nameMainCitiesBefor = null;
        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $description,
            $nameMainCitiesBefor
        );
    }

    public function storageInvoicesUpdates(Request $request)
    {
        info('request->all() for storageInvoicesUpdates:', $request->all());
        $modelId = $request->input('id_invoice');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $userId = $request->input('fk_idUser');

        $userName = null;
        if ($userId) {
            $userName = users::where('id_user', $userId)->first()->nameUser;
        }
        $isApprove = 'o';
        $data = json_decode($request->input('IsAprrove'), true); // Decode the JSON string into an associative array

        if ($data[0]['isApprove'] === '1') {
            $isApprove = 'true';
        } else {
            $isApprove = 'false';
        }
        $routePattern = 'edit_invoices.php';
        $description = "Invoice data changed by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = '(' . $isApprove . ')' . '،تغيير بيانات الفاتورة';
        $model = 'client_invoice';

        $nameMainCitiesBefor = null;
        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $description,
            $nameMainCitiesBefor
        );
    }

    public function addInvoicesUpdateReport(Request $request)
    {
        $modelId = $request->input('id_invoice');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $userId = $request->input('fk_idUser');

        $userName = null;
        if ($userId) {
            $userName = users::where('id_user', $userId)->first()->nameUser;
        }
        $isApprove = 'false';
        if ($request->input('isApprove')) {
            $isApprove = 'true';
        }
        $routePattern = 'updateinvoice.php';
        $description = "Invoice updated by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = '(' . $isApprove . ')' . '،تعديل الفاتورة';
        $model = 'client_invoice';

        $nameMainCitiesBefor = null;
        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $description,
            $nameMainCitiesBefor
        );
    }

    public function addInvoiceProductReport(Request $request)
    {
        $modelId = $request->input('id_invoice_product');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $userId = $request->input('fk_user_update');

        $userName = null;
        if ($userId) {
            $userName = users::where('id_user', $userId)->first()->nameUser;
        }
        $routePattern = 'updateinvoice_product.php';
        $description = "invoice product updated by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = 'تعديل منتجات الفاتورة';
        $model = 'invoice_product';

        $nameMainCitiesBefor = null;
        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $description,
            $nameMainCitiesBefor
        );
    }
}
