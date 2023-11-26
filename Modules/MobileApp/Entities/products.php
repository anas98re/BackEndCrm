<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'id_product',
        'nameProduct',
        'priceProduct',
        'type',
        'fk_country',
        'fk_config',
        'idprd',
        'created_at',
        'fkusercreate',
        'updated_at',
        'fkuserupdate',
        'type_prod_renew'
    ];
}
