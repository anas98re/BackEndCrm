<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice_product extends Model
{
    use HasFactory;

    protected $table = 'invoice_product';

    protected $fillable = [
        'id_invoice_product',
        'fk_id_invoice',
        'fk_product',
        'amount',
        'price',
        'taxtotal',
        'rate_admin',
        'rateUser',
        'idinvoice',
        'name_prod'
    ];
}
