<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_token extends Model
{
    use HasFactory;

    protected $table = 'user_token';

    protected $fillable = [
        'id_token',
        'fkuser',
        'token',
        'date_create'
    ];
}
