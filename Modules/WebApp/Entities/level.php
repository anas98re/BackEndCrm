<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class level extends Model
{
    use HasFactory;

    protected $table = 'level';

    protected $fillable = [
        'id_level',
        'name_level',
        'periorty'
    ];
}
