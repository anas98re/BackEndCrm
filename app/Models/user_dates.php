<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_dates extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usertest',
        'nameusertest',
        'des_usertest',
        'fk_country'
    ];
}
