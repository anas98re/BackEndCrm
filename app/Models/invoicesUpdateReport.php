<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoicesUpdateReport extends Model
{
    use HasFactory;

    protected $table = 'invoices_update_reports';

    public $timestamps = false;

    protected $fillable = [
        'changesData',
        'afterApprove',
        'edit_date',
        'user_id'
    ];
}
