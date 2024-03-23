<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class task_comment extends Model
{
    use HasFactory, Loggable;

    protected $table = 'task_comments';

    protected $fillable = [
        'CommentText',
        'comment_date',
        'task_id',
        'commented_by',
    ];

    public function commented_byUser()
    {
        return $this->belongsTo(User::class, 'commented_by', 'id_user')->select('id_user', 'nameUser');
    }

    public function tasks()
    {
        return $this->belongsTo(task::class, 'task_id', 'id')->select('id', 'title','description');
    }
}
