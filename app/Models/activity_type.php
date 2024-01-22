<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class activity_type extends Model
{
    use HasFactory;

    protected $table = 'activity_type';

    protected $fillable = [
        'id_activity_type',
        'name_activity_type'
    ];

}
