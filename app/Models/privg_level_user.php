<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class privg_level_user extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_privg_user',
        'fk_level',
        'fk_privileg',
        'is_check'
    ];
}
