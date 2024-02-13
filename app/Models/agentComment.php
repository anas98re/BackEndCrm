<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class agentComment extends Model
{
    use HasFactory;

    protected $table = 'agent_comments';
    public $timestamps = false;

    protected $fillable = [
        'agent_id',
        'content',
        'date_comment',
        'user_id'
    ];

    public function agents()
    {
        return $this->belongsTo(agent::class, 'agent_id', 'id_agent');
    }
    public function users()
    {
        return $this->belongsTo(users::class, 'user_id', 'id_user');
    }


}
