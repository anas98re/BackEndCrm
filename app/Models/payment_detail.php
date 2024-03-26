<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class payment_detail extends Model
{
    use HasFactory, LogsActivity, Loggable;

    protected $primaryKey = 'payment_idAdd';
    protected $table = 'payment_details';
    public $timestamps = false;

    protected $fillable = [
        'payment_idAdd',
        'fk_invoice',
        'payment_date',
        'date_updatePayment',
        'amount_paid'
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
            ->useLogName('payment_details Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "payment_details created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "payment_details updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "payment_details deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "payment_details action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }

    public function invoices()
    {
        return $this->belongsTo(client_invoice::class,'id_invoice','fk_invoice');
    }

    public function users()
    {
        return $this->belongsTo(users::class,'payment_idAdd','id_user')->select('id_user','nameUser');
    }
}
