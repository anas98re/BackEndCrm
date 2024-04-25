<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class invoice_product extends Model
{
    use HasFactory, Loggable;
    protected $primaryKey = 'id_invoice_product';

    protected $table = 'invoice_product';
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
        'name_prod'
    ];

    public function product()
    {
        return $this->belongsTo(products::class, 'fk_product');
    }


}
