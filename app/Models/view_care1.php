<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class view_care1 extends Model
{
    use HasFactory;

    protected $table = 'view_care1';

    protected $fillable = [
        'date_approve',
        'id_invoice',
        'type_invoice',
        'name_enterprise',
        'mobile',
        'name_regoin',
        'name_city',
        'namemaincity',
        'date_welcome',
        'type_welcome',
        'user_welcome',
        'type_install',
        'ready_install',
        'date_suspend',
        'user_suspend',
        'date_cancel_suspend',
        'user_cancel_suspend',
        'date_install',
        'user_install',
        'numOfvisit',
        'LastDate_Visit',
        'numOfvisitDone',
        'date_install_1',
        'type_install_1',
        'user_install_1',
        'rate_install_1',
        'date_install_2',
        'type_install_2',
        'user_install_2',
        'rate_install_2'
    ];
}
