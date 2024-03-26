<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class clients extends Model
{
    use HasFactory, Loggable;
    // use HasFactory;

    protected $table = 'clients';
    protected $primaryKey = 'id_clients';

    public $timestamps = false;
    protected $fillable = [
        'id_clients', 'name_client', 'name_enterprise', 'type_job',
        'city', 'location', 'fk_regoin', 'date_create', 'type_client',
        'fk_user', 'date_transfer', 'fkusertrasfer', 'mobile', 'date_changetype',
        'reason_change', 'reason_transfer', 'offer_price', 'date_price', 'user_do',
        'ismarketing', 'address_client', 'date_recive', 'userAdd_email', 'phone',
        'IDcustomer', 'descActivController', 'presystem', 'sourcclient',
        'activity_type_fk', 'user_add', 'date_visit_Client', 'done_transfer',
        'done_visit', 'tag', 'size_activity', 'fk_client_source', 'email',
        'fk_rejectClient', 'SerialNumber', 'is_comments_check','type_record',
        'reason_class','type_classification','date_update', 'fkuser_update','received_date',
        'approveIduser_reject','date_reject','fk_user_reject','date_approve_reject'
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
            ->useLogName('Client Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "Client created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "Client updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "Client deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "Client action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }

}
