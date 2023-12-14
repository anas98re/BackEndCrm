<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class statuse_task_fraction extends Model
{
    use HasFactory;

    protected $table = 'statuse_task_fraction';

    protected $fillable = [
        'changed_date',
        'task_statuse_id',
        'task_id',
        'changed_by',
        'type',
        'priority'
    ];

}
