<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class task_comment extends Model
{
    use HasFactory;

    protected $table = 'task_comments';

    protected $fillable = [
        'CommentText',
        'comment_date',
        'task_id',
        'commented_by',
    ];

}
