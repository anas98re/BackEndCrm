<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class importantLink extends Model
{
    use HasFactory, LogsActivity, Loggable;
    public $timestamps = false;

    protected $fillable = [
        'title',
        'address',
        'link',
        'notes',
        'add_date',
        'edit_date',
        'user_id',
        'clause',
        'department'
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
            ->useLogName('importantLink Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "importantLink created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "importantLink updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "importantLink deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "importantLink action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }
}
