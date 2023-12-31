<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clients extends Model
{
    use HasFactory;

    protected $table = 'clients';

    // protected $fillable = [
    //     'idclients_date',
    //     'date_client_visit',
    //     'fk_user',
    //     'is_done',
    //     'fk_client',
    //     'fk_invoice'
    // ];
    protected $fillable = [
        'id_clients', 'name_client', 'name_enterprise', 'type_job',
        'city', 'location', 'fk_regoin', 'date_create', 'type_client',
        'fk_user', 'date_transfer', 'fkusertrasfer', 'mobile', 'date_changetype',
        'reason_change', 'reason_transfer', 'offer_price', 'date_price', 'user_do',
        'ismarketing', 'address_client', 'date_recive', 'userAdd_email', 'phone',
        'IDcustomer', 'descActivController', 'presystem', 'sourcclient',
        'activity_type_fk', 'user_add', 'date_visit_Client', 'done_transfer',
        'done_visit', 'tag', 'size_activity', 'fk_client_source', 'email',
        'fk_rejectClient', 'SerialNumber', 'is_comments_check'
    ];
}
