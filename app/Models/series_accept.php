<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class series_accept extends Model
{
    use HasFactory, Loggable;

    protected $table = 'series_accept';
    public $timestamps = false;

    protected $fillable = [
        'id_series',
        'name_series',
        'priority',
        'fk_user',
        'fk_country'
    ];
}
