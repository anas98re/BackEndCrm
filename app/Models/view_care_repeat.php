<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class view_care_repeat extends Model
{
    use HasFactory;

    protected $table = 'view_care_repeat';

    protected $fillable = [
        'use_system',
        'rate',
        'date_communication',
        'number_wrong',
        'client_repeat',
        'client_out',
        'school',
        'is_suspend',
        'isRecommendation',
        'is_visit',
        'date_next',
        'name_enterprise',
        'nameUser',
        'date_create',
        'date_approve',
        'dateinstall_done',
        'mobile',
        'fk_regoin',
        'name_regoin',
        'name_client'
    ];
}
