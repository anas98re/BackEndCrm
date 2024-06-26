<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_invoices_view extends Model
{
    use HasFactory, Loggable;

    protected $table = 'product_invoices_view';
    public $timestamps = false;

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
        'name_prod',
        'sourcclient',
        'name_enterprise',
        'ismarketing',
        'name_Regoin_invoice',
        'nameUser',
        'type_invoice',
        'date_create_invoice',
        'date_approve',
        'type'
    ];
}
