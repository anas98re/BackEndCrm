<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class privg_level_user extends Model
{
    use HasFactory;

    protected $table = 'privg_level_user';

    protected $fillable = [
        'id_privg_user',
        'fk_level',
        'fk_privileg',
        'is_check'
    ];
}
