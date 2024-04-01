<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class payment_detail extends Model
{
    use HasFactory, Loggable;

    protected $primaryKey = 'payment_idAdd';
    protected $table = 'payment_details';
    public $timestamps = false;

    protected $fillable = [
        'payment_idAdd',
        'fk_invoice',
        'payment_date',
        'date_updatePayment',
        'amount_paid'
    ];


    public function invoices()
    {
        return $this->belongsTo(client_invoice::class,'id_invoice','fk_invoice');
    }

    public function users()
    {
        return $this->belongsTo(users::class,'payment_idAdd','id_user')->select('id_user','nameUser');
    }
}
