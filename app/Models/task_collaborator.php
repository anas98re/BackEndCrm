<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class task_collaborator extends Model
{
    use HasFactory;

    protected $table = 'task_collaborators';

    protected $fillable = [
        'task_id',
        'collaborator_employee_id'
    ];


}
