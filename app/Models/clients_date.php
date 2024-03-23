<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class clients_date extends Model
{
    use HasFactory, Loggable;
    // use Loggable;

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
        $request = app(Request::class);
        $routePattern = $request->route()->uri();
        $ip = $request->ip();
        $user = auth('sanctum')->user();
        $userName = $user ? $user->nameUser : null;
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->useLogName('clients_date Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "clients_date created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "clients_date updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "clients_date deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "clients_date action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }

}
