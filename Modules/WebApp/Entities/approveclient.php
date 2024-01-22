<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class approveclient extends Model
{
    use HasFactory;

    protected $table = 'approveclient';

    protected $fillable = [
        'id_approveClient',
        'fk_user',
        'fk_client',
        'date_approve',
        'is_approve',
        'fk_invoice',
    ];
}
