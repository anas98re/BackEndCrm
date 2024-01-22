<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class agent extends Model
{
    use HasFactory;

    protected $table = 'agent';

    protected $fillable = [
        'id_agent',
        'name_agent',
        'type_agent',
        'email_egent',
        'mobile_agent',
        'fk_country',
        'description',
        'image_agent'
    ];
}
