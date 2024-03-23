<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    protected $fillable = [
        'model',
        'action',
        'old_data',
        'new_data',
        'description',
        'user_id',
        'model_id',
        'route',
        'ip',
    ];

    // Define any relationships or additional methods for the ChangeLog model
}
