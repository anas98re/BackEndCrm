<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deleteinvoice_info extends Model
{
    use HasFactory;

    protected $table = 'deleteinvoice_info';

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
