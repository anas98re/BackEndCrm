<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class config_table extends Model
{
    use HasFactory;

    protected $table = 'config_table';
    public $timestamps = false;
    
    protected $fillable = [
        'id_config',
        'name_config',
        'value_config',
        'fk_country'
    ];
}
