<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;


class company extends Model
{
    use HasFactory ,Loggable;

    protected $table = 'company';
    public $timestamps = false;
    protected $primaryKey = 'id_Company';

    protected $fillable = [
        'id_Company',
        'name_company',
        'path_logo'
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
            ->useLogName('company Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "company created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "company updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "company deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "company action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }

}
