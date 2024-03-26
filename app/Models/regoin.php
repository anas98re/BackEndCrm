<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class regoin extends Model
{
    use HasFactory, Loggable;

    protected $table = 'regoin';
    public $timestamps = false;

    protected $fillable = [
        'id_regoin',
        'name_regoin',
        'fk_country'
    ];
}
