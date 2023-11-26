<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class series_invoiceaccept extends Model
{
    use HasFactory;

    protected $table = 'series_invoiceaccept';

    protected $fillable = [
        'idApprove_series',
        'fk_user',
        'fk_invoice',
        'is_approve',
        'date_approve',
        'priority_approve'
    ];
}
