<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deleteinvoice extends Model
{
    use HasFactory, Loggable;

    protected $table = 'deleteinvoice';
    public $timestamps = false;

    protected $fillable = [
        'id_deleteInvoice',
        'fk_user',
        'fk_client',
        'date_delete'
    ];
}
