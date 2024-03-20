<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ticket_detail extends Model
{
    use HasFactory;

    protected $table = 'tickets_detail';
    public $timestamps = false;
    protected $primaryKey = 'id_ticket_detail';

    protected $fillable = [
        'id_ticket_detail',
        'fk_ticket',
        'fk_client',
        'type_problem',
        'details_problem',
        'notes_ticket',
        'type_ticket',
        'type_ticket_reopen',
        'fk_user_open',
        'fk_user_close',
        'fk_user_recive',
        'fk_user_reopen',
        'date_reopen',
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
        'categories_ticket_fk',
        'ticket_source_fk'
    ];
}
