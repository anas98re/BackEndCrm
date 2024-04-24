<?php

namespace App\Services;

use App\Models\files_invoice;
use App\Models\notifiaction;
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

    public function __construct(AppSrevices $myService)
    {
        $this->myService = $myService;
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
        $maincityparam = implode(', ', $maincity);

        $data = DB::table('client_invoice AS inv')
            ->select('inv.*', 'us.nameUser', 'cc.name_client', 'cc.name_enterprise', 'cc.fk_regoin', 'rr.name_regoin', 'rrgoin.name_regoin AS name_regoin_invoice', 'cc.type_client', 'cc.mobile', 'cc.ismarketing', 'usr.nameUser AS lastuserupdateName', 'usr1.nameUser AS nameuserinstall', 'usr2.nameUser AS nameuserApprove', 'rr.fk_country', 'usrback.nameUser AS nameuserback', 'userreplay.nameUser AS nameuserreplay', 'usertask.nameUser AS nameusertask', 'cc.city', 'cy.name_city', 'mcit.namemaincity', 'mcit.id_maincity', 'usrinst.nameUser AS nameuser_ready_install', 'usrninst.nameUser AS nameuser_notready_install', 'cc.tag')
            ->join('users AS us', 'us.id_user', '=', 'inv.fk_idUser')
            ->leftJoin('users AS usr', 'usr.id_user', '=', 'inv.lastuserupdate')
            ->leftJoin('users AS usr1', 'usr1.id_user', '=', 'inv.userinstall')
            ->leftJoin('users AS usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
            ->leftJoin('users AS usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
            ->join('clients AS cc', 'cc.id_clients', '=', 'inv.fk_idClient')
            ->join('city AS cy', 'cy.id_city', '=', 'cc.city')
            ->leftJoin('maincity AS mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
            ->leftJoin('users AS usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
            ->leftJoin('users AS usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
            ->leftJoin('users AS userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
            ->leftJoin('users AS usertask', 'usertask.id_user', '=', 'inv.fkusertask')
            ->join('regoin AS rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
            ->join('regoin AS rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
            ->where('rr.fk_country', $fk_country)
            ->whereNull('inv.isdelete')
            ->where('inv.stateclient', 'مشترك')
            ->where('inv.isApprove', 1)
            ->where('mcit.id_maincity', 'IN', $maincityparam)
            ->where('inv.type_seller', '<>', 1)
            ->orderByDesc('inv.date_create')
            ->get();

        $arrJson = [];

        foreach ($data as $row) {
            $arrJson[] = (array) $row;
            $id_invoice = $row->id_invoice;
            $participate_fk = $row->participate_fk;
            $fk_agent = $row->fk_agent;

            if ($participate_fk != null) {
                $participateData = DB::table('participate')
                    ->where('id_participate', $participate_fk)
                    ->get()
                    ->toArray();

                $arrJson[count($arrJson) - 1]['participal_info'] = $participateData;
            }

            if ($fk_agent != null) {
                $agentData = DB::table('agent')
                    ->where('id_agent', $fk_agent)
                    ->get()
                    ->toArray();

                $arrJson[count($arrJson) - 1]['agent_distibutor_info'] = $agentData;
            }

            $productData = DB::table('invoice_product AS i')
                ->select('i.*', 'p.*')
                ->join('products AS p', 'i.fk_product', '=', 'p.id_product')
                ->where('fk_id_invoice', $id_invoice)
                ->get()
                ->toArray();

            $arrJson[count($arrJson) - 1]['products'] = $productData;
        }

        return $arrJson;
    }


    function getInvoicesmaincityMix($fk_country, $maincity, $state)
    {
        $param = '';

        switch ($state) {
            case '0':
                $param = '';
                break;
            case '1':
                $param = ' and inv.isdoneinstall = 1';
                break;
            case 'suspend':
                $param = " and inv.isdoneinstall is null and inv.ready_install = '0' and inv.TypeReadyClient = 'suspend'";
                break;
        }

        if ($state == 'wait') {
            $param = " and inv.isdoneinstall is null and inv.ready_install = '1'";
        }

        $query = DB::table('client_invoice AS inv')
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
            ->join('users AS us', 'us.id_user', '=', 'inv.fk_idUser')
            ->leftJoin('users AS usr', 'usr.id_user', '=', 'inv.lastuserupdate')
            ->leftJoin('users AS usr1', 'usr1.id_user', '=', 'inv.userinstall')
            ->leftJoin('users AS usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
            ->leftJoin('users AS usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
            ->join('clients AS cc', 'cc.id_clients', '=', 'inv.fk_idClient')
            ->join('city AS cy', 'cy.id_city', '=', 'cc.city')
            ->leftJoin('maincity AS mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
            ->leftJoin('users AS usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
            ->leftJoin('users AS usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
            ->leftJoin('users AS userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
            ->leftJoin('users AS usertask', 'usertask.id_user', '=', 'inv.fkusertask')
            ->join('regoin AS rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
            ->join('regoin AS rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
            ->where('rr.fk_country', '=', $fk_country)
            ->whereNull('inv.isdelete')
            ->where('inv.stateclient', '=', 'مشترك')
            ->where('inv.isApprove', '=', 1)
            ->whereRaw($param)
            ->whereIn('mcit.id_maincity', $maincity)
            ->where('inv.type_seller', '<>', 1)
            ->orderBy('inv.date_create', 'desc');

        $invoices = $query->get();

        $data = [];

        foreach ($invoices as $invoice) {
            $id_invoice = $invoice->id_invoice;
            $participate_fk = $invoice->participate_fk;
            $fk_agent = $invoice->fk_agent;

            if (!is_null($participate_fk)) {
                $participate = DB::table('participate')
                    ->where('id_participate', $participate_fk)
                    ->first();

                if (!is_null($participate)) {
                    $invoice->participal_info = $participate;
                }
            }

            $agent_distibutor_info = DB::table('agent')
                ->where('id_agent', $fk_agent)
                ->first();

            if (!is_null($agent_distibutor_info)) {
                $invoice->agent_distibutor_info = $agent_distibutor_info;
            }

            $products = DB::table('invoice_product AS i')
                ->join('products AS p', 'i.fk_product', '=', 'p.id_product')
                ->where('fk_id_invoice', $id_invoice)
                ->get();

            $invoice->products = $products;

            $data[] = $invoice;
        }

        return $data;
    }

    public function getInvoicesCityState($fk_country, $city,  $state)
    {
    }

    public function getInvoicesCity($fk_country, $city)
    {
    }
}
