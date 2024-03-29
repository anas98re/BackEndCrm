<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class taskStatus extends Model
{
    use HasFactory;
    
    protected $table = 'task_statuses';

    protected $fillable = [
        'name',
        'type',
        'priority'
    ];

}
