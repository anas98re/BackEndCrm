<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usertest extends Model
{
    use HasFactory;

    protected $table = 'usertest';

    protected $fillable = [
        'id_usertest',
        'nameusertest',
        'fk_country',
        'des_usertest'
    ];
}
