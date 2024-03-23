<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_dates extends Model
{
    use HasFactory, Loggable;

    protected $table = 'user_dates';
    public $timestamps = false;

    protected $fillable = [
        'id_usertest',
        'nameusertest',
        'des_usertest',
        'fk_country'
    ];
}
