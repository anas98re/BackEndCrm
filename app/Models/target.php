<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class target extends Model
{
    use HasFactory, Loggable;

    protected $table = 'target';
    public $timestamps = false;

    protected $fillable = [
        'id_target',
        'type_target',
        'name_target',
        'year_target',
        'value_target',
        'fk_region',
        'region_name',
        'fk_user_add',
        'fk_user_update'
    ];
}
