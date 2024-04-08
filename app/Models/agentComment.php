<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class agentComment extends Model
{
    use HasFactory, Loggable;

    protected $table = 'agent_comments';
    public $timestamps = false;

    protected $fillable = [
        'agent_id',
        'content',
        'date_comment',
        'user_id',
        'type_comment',
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
