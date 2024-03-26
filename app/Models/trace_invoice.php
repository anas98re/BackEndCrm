<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class trace_invoice extends Model
{
    use HasFactory, Loggable;

    protected $table = 'trace_invoice';
    public $timestamps = false;

    protected $fillable = [
        'idR_invoice',
        'fk_user',
        'date_change',
        'desc_change',
        'fk_invoice',
        'type_change'
    ];
}
