<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class target extends Model
{
    use HasFactory;

    protected $table = 'target';

    protected $fillable = [
        'id_target',
        'type_target',
        'name_target',
        'year_target',
        'value_target',
        'fk_region',
        'region_name'
    ];
}
