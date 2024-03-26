<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class privileges extends Model
{
    use HasFactory, Loggable;

    protected $table = 'privileges';
    public $timestamps = false;

    protected $fillable = [
        'id_privilege',
        'name_privilege',
        'type_prv',
        'periorty'
    ];
}
