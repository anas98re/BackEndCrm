<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class newtabel extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_prod',
        'nameUser',
        'email',
        'isActive',
        'type_administration',
        'type_level',
        'fk_regoin',
        'fkuserAdd',
        'mobile',
        'created_at'
    ];
}
