<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class maincity extends Model
{
    use HasFactory;

    protected $table = 'maincity';

    protected $fillable = [
        'id_maincity',
        'namemaincity',
        'fk_country'
    ];
}
