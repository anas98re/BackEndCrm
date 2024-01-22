<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_dates extends Model
{
    use HasFactory;

    protected $table = 'user_dates';

    protected $fillable = [
        'id_usertest',
        'nameusertest',
        'des_usertest',
        'fk_country'
    ];
}
