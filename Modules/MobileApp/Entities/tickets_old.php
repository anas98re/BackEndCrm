<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tickets_old extends Model
{
    use HasFactory;

    protected $table = 'tickets_old';

    protected $fillable = [
        'id_ticket',
        'fk_client',
        'type_problem',
        'details_problem',
        'notes_ticket',
        'type_ticket',
        'fk_user_open',
        'fk_user_close',
        'fk_user_recive',
        'date_open',
        'date_close',
        'date_recive',
        'client_type',
        'close_id',
        'recive_id',
        'open_id',
        'IDcustomer',
        'tiket_id',
        'name_enterprise',
        'notes_rate',
        'rate',
        'fkuser_rate',
        'date_rate',
        'department',
        'suspend_id',
        'suspend_date'
    ];
}
