<?php

namespace App\Services;

use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceResourceForGetInvoicesByPrivilages;
use App\Models\agent;
use App\Models\client_invoice;
use App\Models\files_invoice;
use App\Models\invoice_product;
use App\Models\notifiaction;
use App\Models\participate;
use App\Models\user_token;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class invoicesSrevices extends JsonResponeService
{
    private $myService;
    private $sqlService;

    public function __construct(AppSrevices $myService, sqlService $sqlService)
    {
        $this->myService = $myService;
        $this->sqlService = $sqlService;
    }

    public function sendNotification($tokens, $id_client, $type, $title, $message)
    {

        foreach ($tokens as $token) {
            Notification::send(
                null,
                new SendNotification(
                    $title,
                    $message,
                    $message,
                    $token,
                )
            );
        }
        return $this;
    }

    public function storeNotification($user_ids, $message, $type, $id_client, $from_user = null)
    {
        foreach ($user_ids as $user_id) {
            $notification = notifiaction::create([
                'message' => $message,
                'type_notify' => $type,
                'to_user' => $user_id,
                'isread' => 0,
                'data' => $id_client,
                'from_user' => is_null($from_user) ? auth()->user()->id_user : $from_user,
                'dateNotify' => Carbon::now('Asia/Riyadh')
            ]);
        }
    }

    public function addAndUpdateInvoiceFiles($filesDelete, $filesAdd, $invoiceId)
    {
        try {
            DB::beginTransaction();
            $response = '';
            if (!empty($filesDelete) && is_array($filesDelete)) {
                foreach ($filesDelete as $fileId) {
                    $fileInvoice = files_invoice::where('id', $fileId)->first();
                    if ($fileInvoice) {
                        $oldFilePath = $fileInvoice->file_attach_invoice;
                        Storage::delete('public/' . $oldFilePath);
                        $fileInvoice->delete();
                    }
                }
                $response = 'deleteed successfully';
            }
            info($filesAdd);
            $fileInvoice = [];
            if (!empty($filesAdd) && is_array($filesAdd)) {
                foreach ($filesAdd as $index => $file) {
                    $filsHandled = $this->myService->handlingfileInvoiceName($file);

                    $fileInvoice[$index] = new files_invoice();
                    $fileInvoice[$index]->file_attach_invoice = $filsHandled;
                    $fileInvoice[$index]->fk_invoice = $invoiceId;
                    $fileInvoice[$index]->type_file = 1;
                    $fileInvoice[$index]->add_date = Carbon::now()->toDateTimeString();
                    $fileInvoice[$index]->save();
                }
                $response = $fileInvoice;
            }
            DB::commit();
            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }



    public function getInvoicesMaincityAllstate($fk_country, $maincity)
    {
        $numbers = explode(',', $maincity);
        $numbers = array_map('trim', $numbers);
        $result = array_map('intval', $numbers);

        $data = client_invoice::with(['user', 'client.regoin', 'client.city.mainCity', 'regoinInvoice', 'invoiceProducts'])
            ->whereHas('client.city.mainCity', function ($query) use ($result) {
                if (is_array($result)) {
                    $query->whereIn('id_maincity', $result);
                } else {
                    $query->where('id_maincity', $result);
                }
            })
            ->whereHas('client.city.regoin', function ($query) use ($fk_country) {
                $query->where('fk_country', $fk_country);
            })
            ->where('isdelete', NULL)
            ->where('stateclient', 'مشترك')
            ->where('isApprove', 1)
            ->where('type_seller', '<>', 1)
            ->orderByDesc('date_create')
            ->get();

        $arrJson = [];

        foreach ($data as $clientInvoice) {
            $invoiceData = $clientInvoice->toArray();

            $participate_fk = $invoiceData['participate_fk'];
            $fk_agent = $invoiceData['fk_agent'];

            if ($participate_fk != null) {
                $participateData = participate::where('id_participate', $participate_fk)->get()->toArray();
                $invoiceData['participal_info'] = $participateData;
            }

            if ($fk_agent != null) {
                $agentData = agent::where('id_agent', $fk_agent)->get()->toArray();
                $invoiceData['agent_distibutor_info'] = $agentData;
            }

            $id_invoice = $invoiceData['id_invoice'];
            $productData = invoice_product::with('product')
                ->where('fk_id_invoice', $id_invoice)
                ->get()
                ->toArray();

            $invoiceData['products'] = $productData;
            $arrJson[] = $invoiceData;
        }


        return InvoiceResource::collection($arrJson);
    }


    public function getInvoicesmaincityMix($fk_country, $maincity, $state)
    {
        $numbers = explode(',', $maincity);
        $numbers = array_map('trim', $numbers);
        $result = array_map('intval', $numbers);

        $invoices = client_invoice::with([
            'user',
            'client.city.mainCity',
            'client.regoin',
            'regoinInvoice',
            'invoiceProducts.product',
            'participalInfo',
            'agentDistibutorInfo'
        ])
            ->whereHas('client.city.mainCity', function ($query) use ($result) {
                if (is_array($result)) {
                    $query->whereIn('id_maincity', $result);
                } else {
                    $query->where('id_maincity', $result);
                }
            })
            ->whereHas('client.regoin', function ($query) use ($fk_country) {
                $query->where('fk_country', $fk_country);
            })
            ->where('isdelete', null)
            ->where('stateclient', 'مشترك')
            ->where('isApprove', 1);

        switch ($state) {
            case '0':
                break;
            case '1':
                $invoices->where('isdoneinstall', 1);
                break;
            case 'suspend':
                $invoices->whereNull('isdoneinstall')
                    ->where('ready_install', '0')
                    ->where('TypeReadyClient', 'suspend');
                break;
            case 'wait':
                $invoices->whereNull('isdoneinstall')
                    ->where('ready_install', '1');
                break;
        }

        $invoices->orderByDesc('date_create');

        $data = $invoices->get();

        return InvoiceResource::collection($data);
    }


    function getInvoicesCityState($fk_country, $state, $city)
    {
        $numbers = explode(',', $city);
        $numbers = array_map('trim', $numbers);
        $result = array_map('intval', $numbers);

        $invoices = client_invoice::with([
            'user',
            'client.city',
            'client.regoin'
        ])
            ->whereHas('client.city', function ($query) use ($result) {
                if (is_array($result)) {
                    $query->whereIn('id_city', $result);
                } else {
                    $query->where('id_city', $result);
                }
            })
            ->whereHas('client.regoin', function ($query) use ($fk_country) {
                $query->where('fk_country', $fk_country);
            })
            ->whereNull('isdelete')
            ->where('stateclient', 'مشترك')
            ->where('isApprove', 1)
            ->where('type_seller', '!=', 1);

        switch ($state) {
            case '0':
                break;
            case '1':
                $invoices->where('isdoneinstall', 1);
                break;
            case 'suspend':
                $invoices->whereNull('isdoneinstall')
                    ->where('ready_install', '0')
                    ->where('TypeReadyClient', 'suspend');
                break;
            case 'wait':
                $invoices->whereNull('isdoneinstall')
                    ->where('ready_install', '1');
                break;
        }

        $invoices = $invoices->orderBy('date_create', 'desc')
            ->get();

        return InvoiceResource::collection($invoices);
    }

    function getInvoicesCity($fk_country, $city)
    {
        $numbers = explode(',', $city);
        $numbers = array_map('trim', $numbers);
        $result = array_map('intval', $numbers);

        $arrJson = client_invoice::with([
            'user',
            'client.city',
            'client.regoin',
            'userUpdated',
            'userInstalled',
            'userReadyInstall',
            'userNotReadyInstall',
            'userApproved',
            'userBack',
            'userReplay',
            'userTask'
        ])
            ->whereHas('client.city', function ($query) use ($result) {
                if (is_array($result)) {
                    $query->whereIn('id_city', $result);
                } else {
                    $query->whereIn('id_city', $result);
                }
            })
            ->whereHas('client.regoin', function ($query) use ($fk_country) {
                $query->where('fk_country', $fk_country);
            })
            ->whereNull('isdelete')
            ->where('stateclient', 'مشترك')
            ->where('isApprove', 1)
            ->where('type_seller', '<>', 1)
            ->orderBy('date_create', 'desc')
            ->get();

        return InvoiceResource::collection($arrJson);
    }
}
