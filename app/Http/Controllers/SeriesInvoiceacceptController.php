<?php

namespace App\Http\Controllers;

use App\Models\series_invoiceaccept;
use App\Http\Requests\Storeseries_invoiceacceptRequest;
use App\Http\Requests\Updateseries_invoiceacceptRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeriesInvoiceacceptController extends Controller
{
    function getSeriesInvoiceAll($status)
    {
        $selectArray = [
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
            'usrninst.nameUser as nameuser_notready_install'
        ];

        if ($status == 'user') {
            $query = DB::table('client_invoice as inv')
                ->select($selectArray)
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
                ->join('series_invoiceaccept as si', 'si.fk_invoice', '=', 'inv.id_invoice') // Added join here
                ->where(function ($query) {
                    info($this->currectUserId);
                        $query->where('si.fk_user', $this->currectUserId);
                })
                ->whereNull('inv.isdelete')
                ->orderByDesc('inv.date_back_now');


            $results = $query->get();
        } else {

            $query = DB::table('client_invoice as inv')
                ->select($selectArray)
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
                ->where(function ($query) use ($status) {
                    if ($status == 0) {
                        $query->where('inv.approve_back_done', 0);
                    }
                    if ($status == 1) {
                        $query->where('inv.approve_back_done', 1);
                    }
                    if ($status == 2) {
                        $query->where('inv.approve_back_done', 2);
                    }
                    if ($status == 'all') {
                        $query->where('inv.approve_back_done', 0)
                            ->orWhere('inv.approve_back_done', 1)
                            ->orWhere('inv.approve_back_done', 2);
                    }
                })
                ->whereNull('inv.isdelete')
                ->orderByDesc('inv.date_back_now');


            $results = $query->get();
        }

        return $results;
    }
}
