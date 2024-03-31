<?php

namespace App\Http\Controllers;

use App\Models\updatesReport;
use App\Http\Requests\StoreupdatesReportRequest;
use App\Http\Requests\UpdateupdatesReportRequest;
use App\Jobs\StorageClientsUpdatesJob;
use App\Jobs\StorageFilesInvoiseDeletedJob;
use App\Jobs\StorageUpdates;
use App\Models\ChangeLog;
use App\Models\files_invoice;
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
            $user = users::where('id_user', $userId)->first();
            if ($user) {
                $userName = $user->nameUser;
            }
        }

        $isApprove = null;
        $routePattern = 'updateuser_patch.php';
        $description = "User updated by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = 'تعديل بيانات المستخدم ';
        $model = 'App\Models\users';

        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $routePattern,
            $description,
            $nameMainCitiesBefor,
            $isApprove
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
            $user = users::where('id_user', $userId)->first();
            if ($user) {
                $userName = $user->nameUser;
            }
        }
        $isApprove = null;
        $routePattern = 'clientUpdate.php';
        $description = "Client updated by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = 'تعديل بيانات العميل ';
        $model = 'App\Models\clients';

        $nameMainCitiesBefor = null;
        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $routePattern,
            $description,
            $nameMainCitiesBefor,
            $isApprove
        );
    }

    public function storageInvoicesUpdates(Request $request)
    {
        $modelId = $request->input('id_invoice');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $userId = $request->input('fk_idUser');

        $userName = null;
        if ($userId) {
            $user = users::where('id_user', $userId)->first();
            if ($user) {
                $userName = $user->nameUser;
            }
        }
        $isApprove = null;
        $data = json_decode($request->input('IsAprrove'), true); // Decode the JSON string into an associative array
        if ($data !== null && count($data) > 0 && isset($data[0]['isApprove'])) {
            if ($data[0]['isApprove'] === '1') {
                $isApprove = 'true';
            } else {
                $isApprove = 'false';
            }
        } else {
            // Handle the case when $data is null or empty
            $isApprove = 'Not Found'; // or any other default value you want to set
        }

        $routePattern = 'edit_invoices.php';
        $description = "Invoice data changed by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = '(' . $isApprove . ')' . '،تغيير بيانات الفاتورة';
        $model = 'App\Models\client_invoice';

        $nameMainCitiesBefor = null;
        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $routePattern,
            $description,
            $nameMainCitiesBefor,
            $isApprove
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
            $user = users::where('id_user', $userId)->first();
            if ($user) {
                $userName = $user->nameUser;
            }
        }
        $isApprove = null;
        $data = json_decode($request->input('IsAprrove'), true); // Decode the JSON string into an associative array

        if ($data !== null && count($data) > 0 && isset($data[0]['isApprove'])) {
            if ($data[0]['isApprove'] === '1') {
                $isApprove = 'true';
            } else {
                $isApprove = 'false';
            }
        } else {
            // Handle the case when $data is null or empty
            $isApprove = 'Not Found'; // or any other default value you want to set
        }

        $routePattern = 'updateinvoice.php';
        $description = "Invoice updated by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = '(' . $isApprove . ')' . '،تعديل الفاتورة';
        $model = 'App\Models\client_invoice';

        $nameMainCitiesBefor = null;
        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $routePattern,
            $description,
            $nameMainCitiesBefor,
            $isApprove
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
            $user = users::where('id_user', $userId)->first();
            if ($user) {
                $userName = $user->nameUser;
            }
        }
        $isApprove = null;
        $routePattern = 'updateinvoice_product.php';
        $description = "invoice product updated by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = 'تعديل منتجات الفاتورة';
        $model = 'App\Models\invoice_product';

        $nameMainCitiesBefor = null;
        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $routePattern,
            $description,
            $nameMainCitiesBefor,
            $isApprove
        );
    }

    public function storageClientCommunicationUpdates(Request $request)
    {
        info('all request storageClientCommunicationUpdates:', $request->all());
        $modelId = $request->input('id_communication');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $userId = $request->input('fk_idUser');

        $userName = null;
        if ($userId) {
            $user = users::where('id_user', $userId)->first();
            if ($user) {
                $userName = $user->nameUser;
            }
        }
        $isApprove = null;
        $routePattern = 'care/updateCommunication.php';
        $description = "Client Communication updated by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = 'تعديل التقييم';

        $model = 'App\Models\client_communication';
        info(1);
        $nameMainCitiesBefor = null;
        StorageUpdates::dispatch(
            $modelId,
            $model,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $userId,
            $update_source,
            $routePattern,
            $description,
            $nameMainCitiesBefor,
            $isApprove
        );
    }

    public function reportDeletedIdsFillesInvoice(Request $request)
    {
        info('all request reportDeletedIdsFillesInvoice:', $request->all());
        $modelId = $request->input('id_invoice');
        // $id_files = json_decode($request->input('id_files'), true)[0];
        $id_files = $request->input('id_files')[0];
        $userId = $request->input('id_user_updated');
        info('id_files:', [$id_files]);

        $userName = null;
        if ($userId) {
            $user = users::where('id_user', $userId)->first();
            if ($user) {
                $userName = $user->nameUser;
            }
        }
        $routePattern = 'FilesInvoice/crud_files_invoice.php';
        $description = "FilesInvoice deleted by $userName, using route: $routePattern from IP: $this->ip.";
        $update_source = 'المرفقات المحذوفة للفواتير';

        $model = 'App\Models\files_invoice';
        info('reportDeletedIdsFillesInvoice 1');

        $dateUpdate = Carbon::now('Asia/Riyadh')->toDateTimeString();

        $data = [];
        foreach ((array)$id_files as $id) {
            $file_attach_invoice = optional(files_invoice::where('id', $id)
                ->first())
                ->file_attach_invoice;
            $data[] = $file_attach_invoice;
        }
        info('data:', [$data]);

        $reportMessage = implode("\n", $data);

        info('reportDeletedIdsFillesInvoice 2');
        ChangeLog::create([
            'model' => $model,
            'action' => 'updated',
            'changesData' => $reportMessage,
            'description' => $description,
            'user_id' => (int) $userId,
            'model_id' => $modelId,
            'edit_date' => $dateUpdate,
            'source' => $update_source,
            'route' => $routePattern,
            'afterApprove' => null,
            'ip' => null
        ]);
    }
}
