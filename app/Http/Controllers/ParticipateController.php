<?php

namespace App\Http\Controllers;

use App\Models\participate;
use App\Http\Requests\StoreparticipateRequest;
use App\Http\Requests\UpdateparticipateRequest;
use App\Models\client_invoice;
use Illuminate\Support\Facades\DB;

class ParticipateController extends Controller
{
    public function getParticipateClints($id)
    {
        $ClientIds = client_invoice::where('participate_fk', $id)->pluck('fk_idClient');
        return $clientsInfo = DB::table('clients as u')
            ->select(
                'u.id_clients',
                'u.name_client',
                'u.name_enterprise',
                'u.type_client',
                'u.fk_regoin',
                'u.fk_user',
                'u.offer_price',
                'u.date_price',
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

    public function getParticipateInvoices($id)
    {
        $invoiceIds = client_invoice::where('participate_fk', $id)->pluck('id_invoice');
        return $clientsInfo = DB::table('client_invoice as CI')
            ->select(
                'CI.id_invoice',
                'CI.date_create',
                'CI.fk_idClient',
                'CI.fk_idUser',
                'CI.notes',
                'CI.total',
                'CI.dateinstall_done',
                'CI.stateclient',
                'CI.date_approve',
                'CI.address_invoice',
                'CI.invoice_source',
                'CI.amount_paid',
                'CI.renew_year',
                'c.nameCountry',
                'r.name_regoin',
                'us.nameUser',
                'r.fk_country'
            )
            ->whereIn('CI.id_invoice', $invoiceIds) // Filter by the client IDs
            ->leftJoin('clients as cl', 'cl.id_clients', '=', 'CI.fk_idClient')
            ->leftJoin('regoin as r', 'r.id_regoin', '=', 'cl.fk_regoin')
            ->leftJoin('country as c', 'c.id_country', '=', 'r.fk_country')
            ->join('users as us', 'us.id_user', '=', 'CI.fk_idUser')
            ->get();
    }
}


