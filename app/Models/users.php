<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class users extends Model
{
    // use HasFactory;
    use HasApiTokens, HasFactory, Notifiable, LogsActivity, Loggable;
    protected $table = 'users';

    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nameUser',
        'email',
        'mobile',
        'code_verfiy',
        'fk_country',
        'type_administration',
        'type_level',
        'fk_regoin',
        'img_image',
        'img_thumbnail',
        'created_at',
        'updated_at',
        'fkuserAdd',
        'fkuserupdate',
        'isActive',
        'salary',
        'email_pluse',
        'maincity_fk'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $request = app(Request::class);
        $routePattern = $request->route()->uri();
        $ip = $request->ip();
        if ($routePattern == 'api/checkEmail') {

            $userName = 'New User';
            return LogOptions::defaults()
                ->logOnly(['*'])
                ->logOnlyDirty()
                ->useLogName('users Log')
                ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                    // Provide the description for the event based on the event name, route pattern, and IP
                    if ($eventName === 'updated')
                        return "otp updated , using route: $routePattern from IP: $ip.";
                });
        }
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }

    public function managements()
    {
        return $this->belongsTo(managements::class, 'type_administration', 'idmange');
    }
    public function regions()
    {
        return $this->belongsTo(regoin::class, 'fk_regoin', 'id_regoin');
    }
}
