<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class participate extends Model
{
    use HasFactory, LogsActivity, Loggable;

    protected $primaryKey = 'id_participate';

    protected $table = 'participate';
    public $timestamps = false;

    protected $fillable = [
        'id_participate',
        'name_participate',
        'mobile_participate',
        'namebank_participate',
        'numberbank_participate'
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
            ->useLogName('participate Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "participate created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "participate updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "participate deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "participate action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }
}
