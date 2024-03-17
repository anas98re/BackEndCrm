<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class client_communication extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'client_communication';
    protected $primaryKey = 'id_communication';
    public $timestamps = false;

    protected $fillable = [
        'id_communication',
        'fk_client',
        'fk_user',
        'date_communication',
        'result',
        'notes',
        'rate',
        'type_communcation',
        'number_wrong',
        'client_repeat',
        'date_next',
        'id_invoice',
        'IDcustomer',
        'user_do',
        'name_enterprisecom',
        'address',
        'type_install',
        'date_last_com_install',
        'client_out',
        'school',
        'is_suspend',
        'isRecommendation',
        'is_visit',
        'user_update',
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
            ->useLogName('client_communication Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "client_communication created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "client_communication updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "client_communication deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "client_communication action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }
}
