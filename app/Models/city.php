<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_city',
        'name_city',
        'fk_maincity',
        'mainc',
    ];
}
