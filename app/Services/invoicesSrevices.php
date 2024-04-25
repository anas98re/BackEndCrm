<?php

namespace App\Services;

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

    // public function getInvoicesMaincityAllstate($fk_country, $maincity)
    // {
    //     $maincityparam = implode(', ', array($maincity));

    //     $query = $this->sqlService->sqlForGetInvoicesMaincityAllstate($fk_country, $maincityparam);

    //     $data = DB::select($query);

    //     $arrJson = [];

    //     foreach ($data as $row) {
    //         $arrJson[] = (array) $row;
    //         $id_invoice = $row->id_invoice;
    //         $participate_fk = $row->participate_fk;
    //         $fk_agent = $row->fk_agent;

    //         if ($participate_fk != null) {
    //             $participateData = DB::table('participate')
    //                 ->where('id_participate', $participate_fk)
    //                 ->get()
    //                 ->toArray();

    //             $arrJson[count($arrJson) - 1]['participal_info'] = $participateData;
    //         }

    //         if ($fk_agent != null) {
    //             $agentData = DB::table('agent')
    //                 ->where('id_agent', $fk_agent)
    //                 ->get()
    //                 ->toArray();

    //             $arrJson[count($arrJson) - 1]['agent_distibutor_info'] = $agentData;
    //         }

    //         $productData = DB::table('invoice_product AS i')
    //             ->select('i.*', 'p.*')
    //             ->join('products AS p', 'i.fk_product', '=', 'p.id_product')
    //             ->where('fk_id_invoice', $id_invoice)
    //             ->get()
    //             ->toArray();

    //         $arrJson[count($arrJson) - 1]['products'] = $productData;
    //     }

    //     return $arrJson;
    // }

    // public function getInvoicesMaincityAllstate($fk_country, $maincity)
    // {

    //     $data = client_invoice::with(['user', 'client.regoin', 'client.city.mainCity', 'regoinInvoice', 'invoiceProducts'])
    //         ->whereHas('client.city.mainCity', function ($query) use ($maincity) {
    //             if (is_array($maincity)) {
    //                 $query->whereIn('id_maincity', $maincity);
    //             } else {
    //                 $query->where('id_maincity', $maincity);
    //             }
    //         })
    //         ->whereHas('client.city.regoin', function ($query) use ($fk_country) {
    //             $query->where('fk_country', $fk_country);
    //         })
    //         ->where('isdelete', NULL)
    //         ->where('stateclient', 'مشترك')
    //         ->where('isApprove', 1)
    //         ->where('type_seller', '<>', 1)
    //         ->orderByDesc('date_create')
    //         ->get();

    //     $arrJson = [];

    //     foreach ($data as $clientInvoice) {
    //         $invoiceData = $clientInvoice->toArray();

    //         $participate_fk = $invoiceData['participate_fk'];
    //         $fk_agent = $invoiceData['fk_agent'];

    //         if ($participate_fk != null) {
    //             $participateData = participate::where('id_participate', $participate_fk)->get()->toArray();
    //             $invoiceData['participal_info'] = $participateData;
    //         }

    //         if ($fk_agent != null) {
    //             $agentData = agent::where('id_agent', $fk_agent)->get()->toArray();
    //             $invoiceData['agent_distibutor_info'] = $agentData;
    //         }

    //         $id_invoice = $invoiceData['id_invoice'];
    //         $productData = invoice_product::with('product')
    //             ->where('fk_id_invoice', $id_invoice)
    //             ->get()
    //             ->toArray();

    //         $invoiceData['products'] = $productData;
    //         $arrJson[] = $invoiceData;
    //     }

    //     return $arrJson;
    // }

    function getInvoicesmaincityAllstate($fk_country, $maincity)
    {
        $index = 0;
        $numbers = explode(',', $maincity);
        $numbers = array_map('trim', $numbers);
        $result1 = array_map('intval', $numbers);

        $query = DB::table('client_invoice as inv')
            ->select(
                'inv.*',
                'us.nameUser',
                'cc.name_client',
                'cc.name_enterprise',
                'cc.fk_regoin',
                'rr.name_regoin',
                'rrgoin.name_regoin as name_regoin_invoice',
                'cc.type_client',
                'cc.mobile',
                'cc.ismarketing',
                'usr.nameUser as lastuserupdateName',
                'usr1.nameUser as nameuserinstall',
                'usr2.nameUser as nameuserApprove',
                'rr.fk_country',
                'usrback.nameUser as nameuserback',
                'userreplay.nameUser as nameuserreplay',
                'usertask.nameUser as nameusertask',
                'cc.city',
                'cy.name_city',
                'mcit.namemaincity',
                'mcit.id_maincity',
                'usrinst.nameUser as nameuser_ready_install',
                'usrninst.nameUser as nameuser_notready_install',
                'cc.tag'
            )
            ->join('users as us', 'us.id_user', '=', 'inv.fk_idUser')
            ->leftJoin('users as usr', 'usr.id_user', '=', 'inv.lastuserupdate')
            ->leftJoin('users as usr1', 'usr1.id_user', '=', 'inv.userinstall')
            ->leftJoin('users as usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
            ->leftJoin('users as usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
            ->join('clients as cc', 'cc.id_clients', '=', 'inv.fk_idClient')
            ->join('city as cy', 'cy.id_city', '=', 'cc.city')
            ->leftJoin('maincity as mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
            ->leftJoin('users as usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
            ->leftJoin('users as usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
            ->leftJoin('users as userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
            ->leftJoin('users as usertask', 'usertask.id_user', '=', 'inv.fkusertask')
            ->join('regoin as rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
            ->join('regoin as rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
            ->where('rr.fk_country', $fk_country)
            ->whereNull('inv.isdelete')
            ->where('inv.stateclient', 'مشترك')
            ->where('inv.isApprove', 1)
            ->whereIn('mcit.id_maincity', $result1);

        $query->where('inv.type_seller', '<>', 1)
            ->orderBy('inv.date_create', 'desc');

        $result = $query->get();
        $arrJson = [];
        $arrJsonProduct = [];
        $arrJsonpart = [];
        $arrJsonagent = [];

        if ($result->count() > 0) {
            foreach ($result as $row) {
                $arrJson[] = (array)$row;
                $id_invoice = $row->id_invoice;
                $participate_fk = $row->participate_fk;
                $fk_agent = $row->fk_agent;

                if ($participate_fk != null) {
                    $getArrayparticipate = [$participate_fk];
                    $result_part = DB::table('participate')
                        ->where('id_participate', $participate_fk)
                        ->get();

                    if ($result_part->count() > 0) {
                        foreach ($result_part as $rowpart) {
                            $arrJsonpart[] = (array)$rowpart;
                        }

                        $arrJson[$index]["participal_info"] = $arrJsonpart;
                        $arrJsonpart = [];
                    }
                }

                $getArrayagent = [$fk_agent];
                $result_agent = DB::table('agent')
                    ->where('id_agent', $fk_agent)
                    ->get();

                if ($result_agent->count() > 0) {
                    foreach ($result_agent as $rowagent) {
                        $arrJsonagent[] = (array)$rowagent;
                    }

                    $arrJson[$index]["agent_distibutor_info"] = $arrJsonagent;
                    $arrJsonagent = [];
                }

                $getArray = [$$id_invoice];
                $result1 = DB::table('invoice_product as i')
                    ->join('products as p', 'i.fk_product', '=', 'p.id_product')
                    ->where('fk_id_invoice', $id_invoice)
                    ->get();

                if ($result1->count() > 0) {
                    foreach ($result1 as $row1) {
                        $arrJsonProduct[] = (array)$row1;
                    }

                    $arrJson[$index]["products"] = $arrJsonProduct;
                    $arrJsonProduct = [];
                }

                $index++;
            }
        }

        return $arrJson;
    }



    // public function getInvoicesmaincityMix($fk_country, $maincity, $state)
    // {

    //     $query = $this->sqlService->sqlForGetInvoicesmaincityMix($fk_country, $maincity, $state);

    //     $invoices = DB::select($query);

    //     $data = [];

    //     foreach ($invoices as $invoice) {
    //         $id_invoice = $invoice->id_invoice;
    //         $participate_fk = $invoice->participate_fk;
    //         $fk_agent = $invoice->fk_agent;

    //         if (!is_null($participate_fk)) {
    //             $participate = DB::table('participate')
    //                 ->where('id_participate', $participate_fk)
    //                 ->first();

    //             if (!is_null($participate)) {
    //                 $invoice->participal_info = $participate;
    //             }
    //         }

    //         $agent_distibutor_info = DB::table('agent')
    //             ->where('id_agent', $fk_agent)
    //             ->first();

    //         if (!is_null($agent_distibutor_info)) {
    //             $invoice->agent_distibutor_info = $agent_distibutor_info;
    //         }

    //         $products = DB::table('invoice_product AS i')
    //             ->join('products AS p', 'i.fk_product', '=', 'p.id_product')
    //             ->where('fk_id_invoice', $id_invoice)
    //             ->get();

    //         $invoice->products = $products;

    //         $data[] = $invoice;
    //     }

    //     return $data;
    // }

    // public function getInvoicesmaincityMix($fk_country, $maincity, $state)
    // {
    //     $data = client_invoice::with([
    //         'user',
    //         'client.city.mainCity',
    //         'client.regoin',
    //         'regoinInvoice',
    //         'invoiceProducts.product',
    //         'participalInfo',
    //         'agentDistibutorInfo'
    //     ])
    //         ->whereHas('client.city.mainCity', function ($query) use ($maincity) {
    //             if (is_array($maincity)) {
    //                 $query->whereIn('id_maincity', $maincity);
    //             } else {
    //                 $query->where('id_maincity', $maincity);
    //             }
    //         })
    //         ->whereHas('client.regoin', function ($query) use ($fk_country) {
    //             $query->where('fk_country', $fk_country);
    //         })
    //         ->where('isdelete', null)
    //         ->where('stateclient', 'مشترك')
    //         ->where('isApprove', 1);

    //     switch ($state) {
    //         case '0':
    //             break;
    //         case '1':
    //             $data->where('isdoneinstall', 1);
    //             break;
    //         case 'suspend':
    //             $data->whereNull('isdoneinstall')
    //                 ->where('ready_install', '0')
    //                 ->where('TypeReadyClient', 'suspend');
    //             break;
    //         case 'wait':
    //             $data->whereNull('isdoneinstall')
    //                 ->where('ready_install', '1');
    //             break;
    //     }

    //     $data->orderByDesc('date_create');

    //     return $data->get();
    // }

    public  function getInvoicesmaincityMix($fk_country, $maincity, $state)
    {
        $index = 0;
        $numbers = explode(',', $maincity);
        $numbers = array_map('trim', $numbers);
        $result1 = array_map('intval', $numbers);
        $query = DB::table('client_invoice as inv')
            ->select(
                'inv.*',
                'us.nameUser',
                'cc.name_client',
                'cc.name_enterprise',
                'cc.fk_regoin',
                'rr.name_regoin',
                'rrgoin.name_regoin as name_regoin_invoice',
                'cc.type_client',
                'cc.mobile',
                'cc.ismarketing',
                'usr.nameUser as lastuserupdateName',
                'usr1.nameUser as nameuserinstall',
                'usr2.nameUser as nameuserApprove',
                'rr.fk_country',
                'usrback.nameUser as nameuserback',
                'userreplay.nameUser as nameuserreplay',
                'usertask.nameUser as nameusertask',
                'cc.city',
                'cy.name_city',
                'mcit.namemaincity',
                'mcit.id_maincity',
                'usrinst.nameUser as nameuser_ready_install',
                'usrninst.nameUser as nameuser_notready_install',
                'cc.tag'
            )
            ->join('users as us', 'us.id_user', '=', 'inv.fk_idUser')
            ->leftJoin('users as usr', 'usr.id_user', '=', 'inv.lastuserupdate')
            ->leftJoin('users as usr1', 'usr1.id_user', '=', 'inv.userinstall')
            ->leftJoin('users as usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
            ->leftJoin('users as usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
            ->join('clients as cc', 'cc.id_clients', '=', 'inv.fk_idClient')
            ->join('city as cy', 'cy.id_city', '=', 'cc.city')
            ->leftJoin('maincity as mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
            ->leftJoin('users as usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
            ->leftJoin('users as usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
            ->leftJoin('users as userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
            ->leftJoin('users as usertask', 'usertask.id_user', '=', 'inv.fkusertask')
            ->join('regoin as rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
            ->join('regoin as rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
            ->where('rr.fk_country', $fk_country)
            ->whereNull('inv.isdelete')
            ->where('inv.stateclient', 'مشترك')
            ->where('inv.isApprove', 1);

        if ($state == 1) {
            $query->where('inv.isdoneinstall', 1);
        } elseif ($state == 'suspend') {
            $query->where('inv.isdoneinstall', null)
                ->where('inv.ready_install', 0)
                ->where('inv.TypeReadyClient', 'suspend');
        } elseif ($state == 'wait') {
            $query->where('inv.isdoneinstall', null)
                ->where('inv.ready_install', 1);
        }

        $query->whereIn('mcit.id_maincity', $result1)
            ->where('inv.type_seller', '<>', 1)
            ->orderBy('inv.date_create', 'desc');


        $result = $query->get();
        $arrJson = [];
        $arrJsonProduct = [];
        $arrJsonpart = [];
        $arrJsonagent = [];
        if ($result->count() > 0) {
            foreach ($result as $row) {
                $arrJson[] = $row;
                $id_invoice = $row->id_invoice;
                $participate_fk = $row->participate_fk;
                $fk_agent = $row->fk_agent;

                if ($participate_fk != null) {
                    $result_part = DB::table('participate')
                        ->where('id_participate', $participate_fk)
                        ->get();

                    if ($result_part->count() > 0) {
                        foreach ($result_part as $rowpart) {
                            $arrJsonpart[] = $rowpart;
                        }
                        $arrJson[$index]->participal_info = $arrJsonpart;
                        $arrJsonpart = [];
                    }
                }

                $result_agent = DB::table('agent')
                    ->where('id_agent', $fk_agent)
                    ->get();

                if ($result_agent->count() > 0) {
                    foreach ($result_agent as $rowagent) {
                        $arrJsonagent[] = $rowagent;
                    }
                    $arrJson[$index]->agent_distibutor_info = $arrJsonagent;
                    $arrJsonagent = [];
                }

                $result1 = DB::table('invoice_product as i')
                    ->join('products as p', 'i.fk_product', '=', 'p.id_product')
                    ->where('fk_id_invoice', $id_invoice)
                    ->get();

                if ($result1->count() > 0) {
                    foreach ($result1 as $row1) {
                        $arrJsonProduct[] = $row1;
                    }
                    $arrJson[$index]->products = $arrJsonProduct;
                    $arrJsonProduct = [];
                }

                $index++;
            }
        }

        return $arrJson;
    }


    // public function getInvoicesCityState($fk_country, $state, $city)
    // {
    //     $param = '';
    //     switch ($state) {
    //         case '0':
    //             $param = '';
    //             break;

    //         case '1':
    //             $param = ' and inv.isdoneinstall=1 ';
    //             break;

    //         case 'suspend':
    //             $param = " and inv.isdoneinstall is null  and inv.ready_install ='0' and inv.TypeReadyClient='suspend'";
    //             break;
    //     }

    //     if ($state == 'wait') {
    //         $param = " and inv.isdoneinstall is null and inv.ready_install ='1' ";
    //     }
    //     info($param);

    //     $cityArray = explode(',', $city);

    //     $query = $this->sqlService->sqlForGetInvoicesCityState($fk_country, $cityArray, $param);

    //     $result = DB::select($query);

    //     $arrJson = json_decode(json_encode($result), true);

    //     return $arrJson;
    // }

    // function getInvoicesCityState($fk_country, $state, $city)
    // {
    //     $arrJson = client_invoice::with([
    //         'user',
    //         'client.city',
    //         'client.regoin'
    //     ])
    //         ->where('client.city', function ($query) use ($city) {
    //             if (is_array($city)) {
    //                 $query->whereIn('id_city', $city);
    //             } else {
    //                 $query->where('id_city', $city);
    //             }
    //         })
    //         ->whereHas('client.regoin', function ($query) use ($fk_country) {
    //             $query->where('fk_country', $fk_country);
    //         })
    //         ->whereNull('isdelete')
    //         ->where('stateclient', 'مشترك')
    //         ->where('isApprove', 1)
    //         ->where('type_seller', '!=', 1);

    //     switch ($state) {
    //         case '0':
    //             break;
    //         case '1':
    //             $arrJson->where('isdoneinstall', 1);
    //             break;
    //         case 'suspend':
    //             $arrJson->whereNull('isdoneinstall')
    //                 ->where('ready_install', '0')
    //                 ->where('TypeReadyClient', 'suspend');
    //             break;
    //         case 'wait':
    //             $arrJson->whereNull('isdoneinstall')
    //                 ->where('ready_install', '1');
    //             break;
    //     }

    //     $arrJson = $arrJson->orderBy('date_create', 'desc')
    //         ->get();

    //     return $arrJson->toArray();
    // }

    function getInvoicesCityState($fk_country, $state, $city)
    {
        $numbers = explode(',', $city);
        $numbers = array_map('trim', $numbers);
        $result1 = array_map('intval', $numbers);

        $query = DB::table('client_invoice as inv')
            ->select(
                'inv.*',
                'us.nameUser',
                'cc.name_client',
                'cc.name_enterprise',
                'cc.fk_regoin',
                'rr.name_regoin',
                'rrgoin.name_regoin as name_regoin_invoice',
                'cc.type_client',
                'cc.mobile',
                'cc.ismarketing',
                'usr.nameUser as lastuserupdateName',
                'usr1.nameUser as nameuserinstall',
                'usr2.nameUser as nameuserApprove',
                'rr.fk_country',
                'usrback.nameUser as nameuserback',
                'userreplay.nameUser as nameuserreplay',
                'usertask.nameUser as nameusertask',
                'cc.city',
                'cy.name_city',
                'mcit.namemaincity',
                'mcit.id_maincity',
                'usrinst.nameUser as nameuser_ready_install',
                'usrninst.nameUser as nameuser_notready_install',
                'cc.tag'
            )
            ->join('users as us', 'us.id_user', '=', 'inv.fk_idUser')
            ->leftJoin('users as usr', 'usr.id_user', '=', 'inv.lastuserupdate')
            ->leftJoin('users as usr1', 'usr1.id_user', '=', 'inv.userinstall')
            ->leftJoin('users as usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
            ->leftJoin('users as usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
            ->join('clients as cc', 'cc.id_clients', '=', 'inv.fk_idClient')
            ->join('city as cy', 'cy.id_city', '=', 'cc.city')
            ->leftJoin('maincity as mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
            ->leftJoin('users as usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
            ->leftJoin('users as usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
            ->leftJoin('users as userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
            ->leftJoin('users as usertask', 'usertask.id_user', '=', 'inv.fkusertask')
            ->join('regoin as rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
            ->join('regoin as rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
            ->where('rr.fk_country', $fk_country)
            ->whereNull('inv.isdelete')
            ->where('inv.stateclient', 'مشترك')
            ->where('inv.isApprove', 1);

        if ($state == 1) {
            $query->where('inv.isdoneinstall', 1);
        } elseif ($state == 'suspend') {
            $query->where('inv.isdoneinstall', null)
                ->where('inv.ready_install', 0)
                ->where('inv.TypeReadyClient', 'suspend');
        } elseif ($state == 'wait') {
            $query->where('inv.isdoneinstall', null)
                ->where('inv.ready_install', 1);
        }

        $query->whereIn('cy.id_city', $result1)
            ->where('inv.type_seller', '!=', 1)
            ->orderBy('inv.date_create', 'desc');

        $result = $query->get();
        $arrJson = $result->toArray();

        return $arrJson;
    }

    // public function getInvoicesCity($fk_country, $city)
    // {
    //     $query = $this->sqlService->sqlForGetInvoicesCity($fk_country, $city);
    //     $result = DB::select($query, [$fk_country, $city]);

    //     $arrJson = json_decode(json_encode($result), true);

    //     return $arrJson;
    // }

    // function getInvoicesCity($fk_country, $city)
    // {
    //     $arrJson = client_invoice::with([
    //         'user',
    //         'client.city',
    //         'client.regoin',
    //         'lastUserUpdate',
    //         'userinstall',
    //         'userReadyInstall',
    //         'userNotReadyInstall',
    //         'userApprove',
    //         'userBack',
    //         'userReplay',
    //         'userTask'
    //     ])
    //         ->whereHas('client.city', function ($query) use ($city) {
    //             if (is_array($city)) {
    //                 info('sssssssss');
    //                 $query->whereIn('id_city', $city);
    //             } else {
    //                 info('mmmmmmmmmmmmm');

    //                 $query->whereIn('id_city', array($city));
    //             }
    //         })
    //         ->whereHas('client.regoin', function ($query) use ($fk_country) {
    //             $query->where('fk_country', $fk_country);
    //         })
    //         ->whereNull('isdelete')
    //         ->where('stateclient', 'مشترك')
    //         ->where('isApprove', 1)
    //         ->where('type_seller', '<>', 1)
    //         ->orderBy('date_create', 'desc')
    //         ->get();

    //     return $arrJson->toArray();
    // }

    function getInvoicesCity($fk_country, $city)
    {
        $numbers = explode(',', $city);
        $numbers = array_map('trim', $numbers);
        $result = array_map('intval', $numbers);

        $query = DB::table('client_invoice as inv')
            ->select(
                'inv.*',
                'us.nameUser',
                'cc.name_client',
                'cc.name_enterprise',
                'cc.fk_regoin',
                'rr.name_regoin',
                'rrgoin.name_regoin as name_regoin_invoice',
                'cc.type_client',
                'cc.mobile',
                'cc.ismarketing',
                'usr.nameUser as lastuserupdateName',
                'usr1.nameUser as nameuserinstall',
                'usr2.nameUser as nameuserApprove',
                'rr.fk_country',
                'usrback.nameUser as nameuserback',
                'userreplay.nameUser as nameuserreplay',
                'usertask.nameUser as nameusertask',
                'cc.city',
                'cy.name_city',
                'mcit.namemaincity',
                'mcit.id_maincity',
                'usrinst.nameUser as nameuser_ready_install',
                'usrninst.nameUser as nameuser_notready_install',
                'cc.tag'
            )
            ->join('users as us', 'us.id_user', '=', 'inv.fk_idUser')
            ->leftJoin('users as usr', 'usr.id_user', '=', 'inv.lastuserupdate')
            ->leftJoin('users as usr1', 'usr1.id_user', '=', 'inv.userinstall')
            ->leftJoin('users as usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
            ->leftJoin('users as usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
            ->join('clients as cc', 'cc.id_clients', '=', 'inv.fk_idClient')
            ->join('city as cy', 'cy.id_city', '=', 'cc.city')
            ->leftJoin('maincity as mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
            ->leftJoin('users as usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
            ->leftJoin('users as usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
            ->leftJoin('users as userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
            ->leftJoin('users as usertask', 'usertask.id_user', '=', 'inv.fkusertask')
            ->join('regoin as rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
            ->join('regoin as rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
            ->where('rr.fk_country', $fk_country)
            ->whereNull('inv.isdelete')
            ->where('inv.stateclient', 'مشترك')
            ->where('inv.isApprove', 1)
            ->where('inv.type_seller', '<>', 1)
            ->whereIn('cy.id_city', $result)
            ->orderBy('inv.date_create', 'desc');

        $result = $query->get();
        $arrJson = $result->toArray();

        return $arrJson;
    }
}
