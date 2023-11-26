<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class config_table extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_config',
        'name_config',
        'value_config',
        'fk_country'
    ];
}
