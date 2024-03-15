<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class clients_date extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'clients_date';
    public $timestamps = false;
    protected $primaryKey = 'idclients_date';

    protected $fillable = [
        'idclients_date',
        'date_client_visit',
        'fk_user',
        'fk_user_add',
        'is_done',
        'date_done',
        'fk_client',
        'fk_invoice',
        'type_date',
        'processReason',
        'fk_user_update',
        'fk_agent',
        'date_end',
        'fk_user_done',
        'date_update_visit'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        info('dddd');
        $request = app(Request::class);
        $routePattern = $request->route()->uri();
        $ip = $request->ip();
        $user = auth('sanctum')->user();
        info('fgh');
        $userName = $user->nameUser;
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->useLogName('Client Date Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "Client Date created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "Client Date updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "Client Date deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "Client Date action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        info('bbbb');
        return $this->table . '.' . $this->primaryKey;
    }
}
