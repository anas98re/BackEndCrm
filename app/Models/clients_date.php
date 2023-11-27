<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clients_date extends Model
{
    use HasFactory;

    protected $table = 'clients_date';

    protected $fillable = [
        'idclients_date',
        'date_client_visit',
        'fk_user',
        'is_done',
        'fk_client',
        'fk_invoice'
    ];
}
