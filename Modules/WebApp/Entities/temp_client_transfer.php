<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class temp_client_transfer extends Model
{
    use HasFactory;

    protected $table = 'temp_client_transfer';

    protected $fillable = [
        'id_temp_client_transfer',
        'fk_usertransfer',
        'fk_userr',
        'nameprise',
        'date_tr'
    ];
}
