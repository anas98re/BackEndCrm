<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class updatesReport extends Model
{
    use HasFactory, Loggable;

    protected $table = 'updates_reports';

    public $timestamps = false;

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
