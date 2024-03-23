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
    use HasFactory, LogsActivity, Loggable;

    protected $table = 'agent_comments';
    public $timestamps = false;

    protected $fillable = [
        'agent_id',
        'content',
        'date_comment',
        'user_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $request = app(Request::class);
        $routePattern = $request->route()->uri();
        $ip = $request->ip();
        $user = auth('sanctum')->user();
        $userName = $user ? $user->nameUser : null;
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->useLogName('agent_comments Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "agent_comments created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "agent_comments updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "agent_comments deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "agent_comments action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }


    public function agents()
    {
        return $this->belongsTo(agent::class, 'agent_id', 'id_agent');
    }
    public function users()
    {
        return $this->belongsTo(users::class, 'user_id', 'id_user');
    }


}
