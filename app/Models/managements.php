<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class managements extends Model
{
    use HasFactory;

    protected $table = 'managements';

    protected $fillable = [
        'idmange',
        'name_mange',
        'fk_country'
    ];
}
