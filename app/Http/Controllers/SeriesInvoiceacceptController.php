<?php

namespace App\Http\Controllers;

use App\Models\series_invoiceAccept;
use App\Http\Requests\Storeseries_invoiceAcceptRequest;
use App\Http\Requests\Updateseries_invoiceAcceptRequest;
use App\Services\SeriesInvoiceacceptSrevices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeriesInvoiceacceptController extends Controller
{
    private $MyService;

    public function __construct(SeriesInvoiceacceptSrevices $MyService)
    {
        $this->MyService = $MyService;
    }

    function getSeriesInvoiceAll()
    {
        $status = request()->query('status');
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

            $results = $this->MyService
                ->filteByCurrentUserForInvoiceaccept($status, $selectArray);
        } else {
            $results = $this->MyService
                ->filteByAnotherStatutesForInvoiceaccept($status, $selectArray);
        }

        return $this->sendSucssas($results);
    }
}
