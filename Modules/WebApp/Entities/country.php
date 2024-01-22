<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    use HasFactory;

    protected $table = 'country';

    protected $fillable = [
        'id_country',
        'nameCountry',
        'currency'
    ];
}
