<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class privileges extends Model
{
    use HasFactory;

    protected $table = 'privileges';

    protected $fillable = [
        'id_privilege',
        'name_privilege',
        'type_prv',
        'periorty'
    ];
}
