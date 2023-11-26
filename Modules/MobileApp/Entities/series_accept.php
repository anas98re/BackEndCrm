<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class series_accept extends Model
{
    use HasFactory;

    protected $table = 'series_accept';

    protected $fillable = [
        'id_series',
        'name_series',
        'priority',
        'fk_user',
        'fk_country'
    ];
}
