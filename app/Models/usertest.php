<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usertest extends Model
{
    use HasFactory, Loggable;

    protected $table = 'usertest';
    public $timestamps = false;

    protected $fillable = [
        'id_usertest',
        'nameusertest',
        'fk_country',
        'des_usertest'
    ];
}
