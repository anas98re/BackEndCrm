<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deleteinvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_deleteInvoice',
        'fk_user',
        'fk_client',
        'date_delete'
    ];
}
