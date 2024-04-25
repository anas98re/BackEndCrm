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

class sqlService extends JsonResponeService
{
      // function getInvoicesmaincityAllstate($fk_country, $maincity)
    // {
    //     $index = 0;
    //     $numbers = explode(',', $maincity);
    //     $numbers = array_map('trim', $numbers);
    //     $result1 = array_map('intval', $numbers);

    //     $query = DB::table('client_invoice as inv')
    //         ->select(
    //             'inv.*',
    //             'us.nameUser',
    //             'cc.name_client',
    //             'cc.name_enterprise',
    //             'cc.fk_regoin',
    //             'rr.name_regoin',
    //             'rrgoin.name_regoin as name_regoin_invoice',
    //             'cc.type_client',
    //             'cc.mobile',
    //             'cc.ismarketing',
    //             'usr.nameUser as lastuserupdateName',
    //             'usr1.nameUser as nameuserinstall',
    //             'usr2.nameUser as nameuserApprove',
    //             'rr.fk_country',
    //             'usrback.nameUser as nameuserback',
    //             'userreplay.nameUser as nameuserreplay',
    //             'usertask.nameUser as nameusertask',
    //             'cc.city',
    //             'cy.name_city',
    //             'mcit.namemaincity',
    //             'mcit.id_maincity',
    //             'usrinst.nameUser as nameuser_ready_install',
    //             'usrninst.nameUser as nameuser_notready_install',
    //             'cc.tag'
    //         )
    //         ->join('users as us', 'us.id_user', '=', 'inv.fk_idUser')
    //         ->leftJoin('users as usr', 'usr.id_user', '=', 'inv.lastuserupdate')
    //         ->leftJoin('users as usr1', 'usr1.id_user', '=', 'inv.userinstall')
    //         ->leftJoin('users as usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
    //         ->leftJoin('users as usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
    //         ->join('clients as cc', 'cc.id_clients', '=', 'inv.fk_idClient')
    //         ->join('city as cy', 'cy.id_city', '=', 'cc.city')
    //         ->leftJoin('maincity as mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
    //         ->leftJoin('users as usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
    //         ->leftJoin('users as usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
    //         ->leftJoin('users as userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
    //         ->leftJoin('users as usertask', 'usertask.id_user', '=', 'inv.fkusertask')
    //         ->join('regoin as rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
    //         ->join('regoin as rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
    //         ->where('rr.fk_country', $fk_country)
    //         ->whereNull('inv.isdelete')
    //         ->where('inv.stateclient', 'مشترك')
    //         ->where('inv.isApprove', 1)
    //         ->whereIn('mcit.id_maincity', $result1);

    //     $query->where('inv.type_seller', '<>', 1)
    //         ->orderBy('inv.date_create', 'desc');

    //     $result = $query->get();
    //     $arrJson = [];
    //     $arrJsonProduct = [];
    //     $arrJsonpart = [];
    //     $arrJsonagent = [];

    //     if ($result->count() > 0) {
    //         foreach ($result as $row) {
    //             $arrJson[] = (array)$row;
    //             $id_invoice = $row->id_invoice;
    //             $participate_fk = $row->participate_fk;
    //             $fk_agent = $row->fk_agent;

    //             if ($participate_fk != null) {
    //                 $getArrayparticipate = [$participate_fk];
    //                 $result_part = DB::table('participate')
    //                     ->where('id_participate', $participate_fk)
    //                     ->get();

    //                 if ($result_part->count() > 0) {
    //                     foreach ($result_part as $rowpart) {
    //                         $arrJsonpart[] = (array)$rowpart;
    //                     }

    //                     $arrJson[$index]["participal_info"] = $arrJsonpart;
    //                     $arrJsonpart = [];
    //                 }
    //             }

    //             $getArrayagent = [$fk_agent];
    //             $result_agent = DB::table('agent')
    //                 ->where('id_agent', $fk_agent)
    //                 ->get();

    //             if ($result_agent->count() > 0) {
    //                 foreach ($result_agent as $rowagent) {
    //                     $arrJsonagent[] = (array)$rowagent;
    //                 }

    //                 $arrJson[$index]["agent_distibutor_info"] = $arrJsonagent;
    //                 $arrJsonagent = [];
    //             }

    //             $getArray = [$$id_invoice];
    //             $result1 = DB::table('invoice_product as i')
    //                 ->join('products as p', 'i.fk_product', '=', 'p.id_product')
    //                 ->where('fk_id_invoice', $id_invoice)
    //                 ->get();

    //             if ($result1->count() > 0) {
    //                 foreach ($result1 as $row1) {
    //                     $arrJsonProduct[] = (array)$row1;
    //                 }

    //                 $arrJson[$index]["products"] = $arrJsonProduct;
    //                 $arrJsonProduct = [];
    //             }

    //             $index++;
    //         }
    //     }

    //     return $arrJson;
    // }



    // public  function getInvoicesmaincityMix($fk_country, $maincity, $state)
    // {
    //     $index = 0;
    //     $numbers = explode(',', $maincity);
    //     $numbers = array_map('trim', $numbers);
    //     $result1 = array_map('intval', $numbers);
    //     $query = DB::table('client_invoice as inv')
    //         ->select(
    //             'inv.*',
    //             'us.nameUser',
    //             'cc.name_client',
    //             'cc.name_enterprise',
    //             'cc.fk_regoin',
    //             'rr.name_regoin',
    //             'rrgoin.name_regoin as name_regoin_invoice',
    //             'cc.type_client',
    //             'cc.mobile',
    //             'cc.ismarketing',
    //             'usr.nameUser as lastuserupdateName',
    //             'usr1.nameUser as nameuserinstall',
    //             'usr2.nameUser as nameuserApprove',
    //             'rr.fk_country',
    //             'usrback.nameUser as nameuserback',
    //             'userreplay.nameUser as nameuserreplay',
    //             'usertask.nameUser as nameusertask',
    //             'cc.city',
    //             'cy.name_city',
    //             'mcit.namemaincity',
    //             'mcit.id_maincity',
    //             'usrinst.nameUser as nameuser_ready_install',
    //             'usrninst.nameUser as nameuser_notready_install',
    //             'cc.tag'
    //         )
    //         ->join('users as us', 'us.id_user', '=', 'inv.fk_idUser')
    //         ->leftJoin('users as usr', 'usr.id_user', '=', 'inv.lastuserupdate')
    //         ->leftJoin('users as usr1', 'usr1.id_user', '=', 'inv.userinstall')
    //         ->leftJoin('users as usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
    //         ->leftJoin('users as usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
    //         ->join('clients as cc', 'cc.id_clients', '=', 'inv.fk_idClient')
    //         ->join('city as cy', 'cy.id_city', '=', 'cc.city')
    //         ->leftJoin('maincity as mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
    //         ->leftJoin('users as usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
    //         ->leftJoin('users as usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
    //         ->leftJoin('users as userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
    //         ->leftJoin('users as usertask', 'usertask.id_user', '=', 'inv.fkusertask')
    //         ->join('regoin as rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
    //         ->join('regoin as rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
    //         ->where('rr.fk_country', $fk_country)
    //         ->whereNull('inv.isdelete')
    //         ->where('inv.stateclient', 'مشترك')
    //         ->where('inv.isApprove', 1);

    //     if ($state == 1) {
    //         $query->where('inv.isdoneinstall', 1);
    //     } elseif ($state == 'suspend') {
    //         $query->where('inv.isdoneinstall', null)
    //             ->where('inv.ready_install', 0)
    //             ->where('inv.TypeReadyClient', 'suspend');
    //     }
    //     if ($state == 'wait') {
    //         $query->where('inv.isdoneinstall', null)
    //             ->where('inv.ready_install', 1);
    //     }

    //     $query->whereIn('mcit.id_maincity', $result1)
    //         ->where('inv.type_seller', '<>', 1)
    //         ->orderBy('inv.date_create', 'desc');


    //     $result = $query->get();
    //     $arrJson = [];
    //     $arrJsonProduct = [];
    //     $arrJsonpart = [];
    //     $arrJsonagent = [];
    //     if ($result->count() > 0) {
    //         foreach ($result as $row) {
    //             $arrJson[] = $row;
    //             $id_invoice = $row->id_invoice;
    //             $participate_fk = $row->participate_fk;
    //             $fk_agent = $row->fk_agent;

    //             if ($participate_fk != null) {
    //                 $result_part = DB::table('participate')
    //                     ->where('id_participate', $participate_fk)
    //                     ->get();

    //                 if ($result_part->count() > 0) {
    //                     foreach ($result_part as $rowpart) {
    //                         $arrJsonpart[] = $rowpart;
    //                     }
    //                     $arrJson[$index]->participal_info = $arrJsonpart;
    //                     $arrJsonpart = [];
    //                 }
    //             }

    //             $result_agent = DB::table('agent')
    //                 ->where('id_agent', $fk_agent)
    //                 ->get();

    //             if ($result_agent->count() > 0) {
    //                 foreach ($result_agent as $rowagent) {
    //                     $arrJsonagent[] = $rowagent;
    //                 }
    //                 $arrJson[$index]->agent_distibutor_info = $arrJsonagent;
    //                 $arrJsonagent = [];
    //             }

    //             $result1 = DB::table('invoice_product as i')
    //                 ->join('products as p', 'i.fk_product', '=', 'p.id_product')
    //                 ->where('fk_id_invoice', $id_invoice)
    //                 ->get();

    //             if ($result1->count() > 0) {
    //                 foreach ($result1 as $row1) {
    //                     $arrJsonProduct[] = $row1;
    //                 }
    //                 $arrJson[$index]->products = $arrJsonProduct;
    //                 $arrJsonProduct = [];
    //             }

    //             $index++;
    //         }
    //     }

    //     return $arrJson;
    // }

   // function getInvoicesCityState($fk_country, $state, $city)
    // {
    //     $numbers = explode(',', $city);
    //     $numbers = array_map('trim', $numbers);
    //     $result1 = array_map('intval', $numbers);

    //     $query = DB::table('client_invoice as inv')
    //         ->select(
    //             'inv.*',
    //             'us.nameUser',
    //             'cc.name_client',
    //             'cc.name_enterprise',
    //             'cc.fk_regoin',
    //             'rr.name_regoin',
    //             'rrgoin.name_regoin as name_regoin_invoice',
    //             'cc.type_client',
    //             'cc.mobile',
    //             'cc.ismarketing',
    //             'usr.nameUser as lastuserupdateName',
    //             'usr1.nameUser as nameuserinstall',
    //             'usr2.nameUser as nameuserApprove',
    //             'rr.fk_country',
    //             'usrback.nameUser as nameuserback',
    //             'userreplay.nameUser as nameuserreplay',
    //             'usertask.nameUser as nameusertask',
    //             'cc.city',
    //             'cy.name_city',
    //             'mcit.namemaincity',
    //             'mcit.id_maincity',
    //             'usrinst.nameUser as nameuser_ready_install',
    //             'usrninst.nameUser as nameuser_notready_install',
    //             'cc.tag'
    //         )
    //         ->join('users as us', 'us.id_user', '=', 'inv.fk_idUser')
    //         ->leftJoin('users as usr', 'usr.id_user', '=', 'inv.lastuserupdate')
    //         ->leftJoin('users as usr1', 'usr1.id_user', '=', 'inv.userinstall')
    //         ->leftJoin('users as usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
    //         ->leftJoin('users as usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
    //         ->join('clients as cc', 'cc.id_clients', '=', 'inv.fk_idClient')
    //         ->join('city as cy', 'cy.id_city', '=', 'cc.city')
    //         ->leftJoin('maincity as mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
    //         ->leftJoin('users as usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
    //         ->leftJoin('users as usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
    //         ->leftJoin('users as userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
    //         ->leftJoin('users as usertask', 'usertask.id_user', '=', 'inv.fkusertask')
    //         ->join('regoin as rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
    //         ->join('regoin as rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
    //         ->where('rr.fk_country', $fk_country)
    //         ->whereNull('inv.isdelete')
    //         ->where('inv.stateclient', 'مشترك')
    //         ->where('inv.isApprove', 1);

    //     if ($state == 1) {
    //         $query->where('inv.isdoneinstall', 1);
    //     } elseif ($state == 'suspend') {
    //         $query->where('inv.isdoneinstall', null)
    //             ->where('inv.ready_install', 0)
    //             ->where('inv.TypeReadyClient', 'suspend');
    //     }
    //     if ($state == 'wait') {
    //         $query->where('inv.isdoneinstall', null)
    //             ->where('inv.ready_install', 1);
    //     }

    //     $query->whereIn('cy.id_city', $result1)
    //         ->where('inv.type_seller', '!=', 1)
    //         ->orderBy('inv.date_create', 'desc');

    //     $result = $query->get();
    //     $arrJson = $result->toArray();

    //     return $arrJson;
    // }

    // public function getInvoicesCity($fk_country, $city)
    // {
    //     $query = $this->sqlService->sqlForGetInvoicesCity($fk_country, $city);
    //     $result = DB::select($query, [$fk_country, $city]);

    //     $arrJson = json_decode(json_encode($result), true);

    //     return $arrJson;
    // }

     // function getInvoicesCity($fk_country, $city)
    // {
    //     $numbers = explode(',', $city);
    //     $numbers = array_map('trim', $numbers);
    //     $result = array_map('intval', $numbers);

    //     $query = DB::table('client_invoice as inv')
    //         ->select(
    //             'inv.*',
    //             'us.nameUser',
    //             'cc.name_client',
    //             'cc.name_enterprise',
    //             'cc.fk_regoin',
    //             'rr.name_regoin',
    //             'rrgoin.name_regoin as name_regoin_invoice',
    //             'cc.type_client',
    //             'cc.mobile',
    //             'cc.ismarketing',
    //             'usr.nameUser as lastuserupdateName',
    //             'usr1.nameUser as nameuserinstall',
    //             'usr2.nameUser as nameuserApprove',
    //             'rr.fk_country',
    //             'usrback.nameUser as nameuserback',
    //             'userreplay.nameUser as nameuserreplay',
    //             'usertask.nameUser as nameusertask',
    //             'cc.city',
    //             'cy.name_city',
    //             'mcit.namemaincity',
    //             'mcit.id_maincity',
    //             'usrinst.nameUser as nameuser_ready_install',
    //             'usrninst.nameUser as nameuser_notready_install',
    //             'cc.tag'
    //         )
    //         ->join('users as us', 'us.id_user', '=', 'inv.fk_idUser')
    //         ->leftJoin('users as usr', 'usr.id_user', '=', 'inv.lastuserupdate')
    //         ->leftJoin('users as usr1', 'usr1.id_user', '=', 'inv.userinstall')
    //         ->leftJoin('users as usrinst', 'usrinst.id_user', '=', 'inv.user_ready_install')
    //         ->leftJoin('users as usrninst', 'usrninst.id_user', '=', 'inv.user_not_ready_install')
    //         ->join('clients as cc', 'cc.id_clients', '=', 'inv.fk_idClient')
    //         ->join('city as cy', 'cy.id_city', '=', 'cc.city')
    //         ->leftJoin('maincity as mcit', 'mcit.id_maincity', '=', 'cy.fk_maincity')
    //         ->leftJoin('users as usr2', 'usr2.id_user', '=', 'inv.iduser_approve')
    //         ->leftJoin('users as usrback', 'usrback.id_user', '=', 'inv.fkuser_back')
    //         ->leftJoin('users as userreplay', 'userreplay.id_user', '=', 'inv.fkuserdatareplay')
    //         ->leftJoin('users as usertask', 'usertask.id_user', '=', 'inv.fkusertask')
    //         ->join('regoin as rr', 'rr.id_regoin', '=', 'cc.fk_regoin')
    //         ->join('regoin as rrgoin', 'rrgoin.id_regoin', '=', 'inv.fk_regoin_invoice')
    //         ->where('rr.fk_country', $fk_country)
    //         // ->whereNull('inv.isdelete')
    //         ->where('inv.isdelete', null)
    //         ->where('inv.stateclient', 'مشترك')
    //         ->where('inv.isApprove', 1)
    //         ->where('inv.type_seller', '!=', 1)
    //         ->whereIn('cy.id_city', $result)
    //         ->orderBy('inv.date_create', 'desc');

    //     $result = $query->get();
    //     $arrJson = $result->toArray();

    //     return $arrJson;
    // }
}


