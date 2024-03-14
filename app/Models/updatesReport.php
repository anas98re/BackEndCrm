<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class updatesReport extends Model
{
    use HasFactory;

    protected $table = 'updates_reports';

    protected $fillable = [
        'changesData',
        'model',
        'model_id',
        'user_id',
        'edit_date',
        'source',
        'description',
        'afterApprove'
    ];
}
