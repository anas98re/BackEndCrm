<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class client_communication_new extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_communication',
        'fk_client',
        'fk_user',
        'date_communication',
        'result',
        'notes',
        'rate',
        'type_communcation',
        'number_wrong',
        'client_repeat',
        'date_next',
        'id_invoice',
        'IDcustomer',
        'user_do',
        'name_enterprisecom',
        'address',
        'type_install',
        'date_last_com_install',
        'client_out',
        'school',
        'is_suspend',
        'isRecommendation',
        'is_visit',
        'user_update',
    ];
}
