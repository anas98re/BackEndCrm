<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deleteinvoice extends Model
{
    use HasFactory;

    protected $table = 'deleteinvoice';

    protected $fillable = [
        'id_deleteInvoice',
        'fk_user',
        'fk_client',
        'date_delete'
    ];
}
