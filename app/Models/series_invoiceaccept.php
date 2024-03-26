<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class series_invoiceAccept extends Model
{
    use HasFactory, Loggable;

    protected $table = 'series_invoiceAccept';
    public $timestamps = false;

    protected $fillable = [
        'idApprove_series',
        'fk_user',
        'fk_invoice',
        'is_approve',
        'date_approve',
        'priority_approve',
        'notes_approve'
    ];
}
