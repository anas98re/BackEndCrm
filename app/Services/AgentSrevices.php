<?php

namespace App\Services;

use App\Constants;
use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\clients;
use App\Models\notifiaction;
use App\Models\regoin;
use App\Models\tsks_group;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AgentSrevices extends JsonResponeService
{
    public function getAgentClintsService($ClientIds)
    {
        return DB::table('clients as u')
            ->select(
                'u.id_clients',
                'u.name_client',
                'u.name_enterprise',
                'u.type_client',
                'u.fk_regoin',
                'u.fk_user',
                'u.offer_price',
                'u.date_price',
                'u.date_create',
                'u.tag',
                'c.nameCountry',
                'r.name_regoin',
                'us.nameUser',
                'r.fk_country'
            )
            ->whereIn('u.id_clients', $ClientIds) // Filter by the client IDs
            ->leftJoin('regoin as r', 'r.id_regoin', '=', 'u.fk_regoin')
            ->leftJoin('country as c', 'c.id_country', '=', 'r.fk_country')
            ->join('users as us', 'us.id_user', '=', 'u.fk_user')
            ->leftJoin('users as uuserss', 'uuserss.id_user', '=', 'u.user_add')
            ->get();
    }

    public function getAgentInvoicesService($invoiceIds)
    {
        return DB::table('client_invoice as CI')
            ->select(
                'CI.id_invoice',
                'CI.date_create',
                'CI.fk_idClient',
                'CI.fk_idUser',
                'CI.notes',
                'CI.total',
                'CI.dateinstall_done',
                'CI.date_approve',
                'CI.address_invoice',
                'CI.invoice_source',
                'CI.amount_paid',
                'CI.renew_year',
                'CI.Date_FApprove',
                'CI.stateclient',
                'CI.approve_back_done',
                'CI.isApprove',
                'r.name_regoin',
                'CI.name_enterpriseinv',
                'CI.currency_name',
                'c.nameCountry',
                'r.name_regoin',
                'us.nameUser',
                'r.fk_country',
                'cl.name_enterprise',
                // ,,,,
            )
            ->whereIn('CI.id_invoice', $invoiceIds) // Filter by the client IDs
            ->leftJoin('clients as cl', 'cl.id_clients', '=', 'CI.fk_idClient')
            ->leftJoin('regoin as r', 'r.id_regoin', '=', 'cl.fk_regoin')
            ->leftJoin('country as c', 'c.id_country', '=', 'r.fk_country')
            ->join('users as us', 'us.id_user', '=', 'CI.fk_idUser')
            ->get();
    }
}
