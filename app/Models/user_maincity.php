<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_maincity extends Model
{
    use HasFactory;

    protected $table = 'user_maincity';
    public $timestamps = false;

    protected $fillable = [
        'iduser_maincity',
        'fk_maincity',
        'fk_user'
    ];
}
