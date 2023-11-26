<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transferticket extends Model
{
    use HasFactory;

    protected $table = 'transferticket';

    protected $fillable = [
        'id_tr_ticket',
        'resoantransfer_ticket',
        'fkuser_to',
        'fkuserfrom',
        'date_assigntr',
        'fk_ticket'
    ];
}
