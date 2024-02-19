<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_detail extends Model
{
    use HasFactory;

    protected $table = 'payment_details';
    public $timestamps = false;
    protected $primaryKey = 'payment_idAdd';

    protected $fillable = [
        'payment_idAdd',
        'fk_invoice',
        'payment_date',
        'date_updatePayment',
        'amount_paid'
    ];
}
