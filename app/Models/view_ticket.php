<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class view_ticket extends Model
{
    use HasFactory;

    protected $table = 'view_ticket';

    protected $fillable = [
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
        'id_ticket',
        'name_enterprisetc',
        'notes_rate',
        'rate',
        'fkuser_rate',
        'date_rate',
        'department',
        'suspend_id',
        'suspend_date',
        'name_client',
        'name_enterprise',
        'name_regoin',
        'fk_country',
        'nameuseropen',
        'nameuserrecive',
        'nameuserclose',
        'nameuserrate',
        'mobile'
    ];
}
