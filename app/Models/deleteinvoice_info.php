<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deleteinvoice_info extends Model
{
    use HasFactory, Loggable;

    protected $table = 'deleteinvoice_info';
    public $timestamps = false;

    protected $fillable = [
        'id_info',
        'fkinvoice',
        'nameprod',
        'amount',
        'price',
        'taxtotal',
        'rate_admin',
        'rate_user'
    ];
}
