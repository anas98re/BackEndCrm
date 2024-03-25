<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    protected $fillable = [
        'model',
        'action',
        'changesData',
        'description',
        'user_id',
        'model_id',
        'edit_date',
        'route',
        'ip',
        'afterApprove'
    ];

    // Define any relationships or additional methods for the ChangeLog model
}
