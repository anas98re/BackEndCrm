<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class regoin extends Model
{
    use HasFactory;

    protected $table = 'regoin';

    protected $fillable = [
        'id_regoin',
        'name_regoin',
        'fk_country'
    ];
}
