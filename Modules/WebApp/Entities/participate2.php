<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class participate2 extends Model
{
    use HasFactory;

    protected $table = 'participate2';

    protected $fillable = [
        'id_participate',
        'name_participate',
        'mobile_participate',
        'namebank_participate',
        'numberbank_participate'
    ];
}
