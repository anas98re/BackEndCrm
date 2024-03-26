<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class products extends Model
{
    use HasFactory, LogsActivity, Loggable;

    protected $primaryKey = 'id_product';

    protected $table = 'products';
    public $timestamps = false;

    protected $fillable = [
        'id_product',
        'nameProduct',
        'priceProduct',
        'type',
        'fk_country',
        'fk_config',
        'idprd',
        'created_at',
        'fkusercreate',
        'updated_at',
        'fkuserupdate',
        'type_prod_renew'
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
            ->useLogName('products Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "products created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "products updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "products deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "products action occurred, using route: $routePattern from IP: $ip.";
            });
    }


    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }

    public function user()
    {
        return $this->belongsTo(users::class, 'fkuser', 'id_user');
    }
}
