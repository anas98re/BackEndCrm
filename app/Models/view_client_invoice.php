<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class view_client_invoice extends Model
{
    use HasFactory;

    protected $table = 'view_client_invoice';
    public $timestamps = false;
    
    protected $fillable = [
        'date_create_client',
        'IDcustomer',
        'name_enterprise_client',
        'type_client',
        'name_regoin',
        'name_regoinClient',
        'nameUser',
        'nameUser_invoice',
        'name_activity_type',
        'sourcclient',
        'type_invoice',
        'date_create_invoice',
        'date_approve',
        'type_seller',
        'currency',
        'name_agent',
        'rate_agent_participate',
        'name_city',
        'prev_company',
        'total_program',
        'amount_paid',
        'total_devices',
        'count_invoices',
        'totalconvertcurrency',
        'total',
        'type_install'
    ];
}
