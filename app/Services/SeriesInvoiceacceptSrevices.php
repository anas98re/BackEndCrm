<?php

namespace App\Services;

use App\Models\files_invoice;
use App\Services\JsonResponeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SeriesInvoiceacceptSrevices extends JsonResponeService
{
    public function filteByCurrentUserForInvoiceaccept($status, $selectArray)
    {
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
            ->join('series_invoiceAccept as si', 'si.fk_invoice', '=', 'inv.id_invoice') // Added join here
            ->where(function ($query) {
                $query->where('si.fk_user', $this->currectUserId)
                    ->where(function ($q) {
                        $q->whereNull('si.is_approve');
                    })
                    ->where(function ($q) {
                        $q->where(
                            DB::raw('(SELECT is_approve
                                FROM series_invoiceAccept
                                WHERE idApprove_series < si.idApprove_series
                                ORDER BY idApprove_series DESC LIMIT 1)'),
                            '=',
                            1
                        );
                    });
            })
            ->whereNull('inv.isdelete')
            ->orderByDesc('inv.date_back_now');


        return $results = $query->get();
    }

    public function filteByAnotherStatutesForInvoiceaccept($status, $selectArray)
    {
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


        return $results = $query->get();
    }
}
