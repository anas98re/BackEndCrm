<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_token extends Model
{
    use HasFactory, Loggable;
    public $timestamps = false;
    protected $table = 'user_token';

    protected $fillable = [
        'id_token',
        'fkuser',
        'token',
        'date_create'
    ];
}
