<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class series_accept extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_series',
        'name_series',
        'priority',
        'fk_user',
        'fk_country'
    ];
}
