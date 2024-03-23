<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class taskStatus extends Model
{
    use HasFactory, Loggable;

    protected $table = 'task_statuses';

    protected $fillable = [
        'name',
        'type',
        'priority'
    ];

}
