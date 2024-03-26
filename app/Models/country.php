<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    use HasFactory, Loggable;

    protected $table = 'country';
    public $timestamps = false;

    protected $fillable = [
        'id_country',
        'nameCountry',
        'currency'
    ];
}
