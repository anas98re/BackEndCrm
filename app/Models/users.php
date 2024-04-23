<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;
use Modules\MobileApp\Entities\level;

class users extends Model
{
    // use HasFactory;
    use HasApiTokens, HasFactory, Notifiable, Loggable;
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


    public function managements()
    {
        return $this->belongsTo(managements::class, 'type_administration', 'idmange');
    }

    public function regions()
    {
        return $this->belongsTo(regoin::class, 'fk_regoin', 'id_regoin');
    }

    public function country()
    {
        return $this->belongsTo(country::class, 'fk_country', 'id_country');
    }

    public function level()
    {
        return $this->belongsTo(level::class, 'type_level', 'id_level');
    }

    public function fcmToken(): HasMany
    {
        return $this->hasMany(user_token::class, 'fkuser');
    }

    public function privileges(): HasMany
    {
        return $this->hasMany(privg_level_user::class, 'fk_level', 'type_level')
            ->join('privileges', 'privileges.id_privilege', '=', 'privg_level_user.fk_privileg')
            ->orderBy('privileges.periorty', 'asc');
    }

    public function mainCity()
    {
        return $this->hasMany(user_maincity::class, 'fk_user')
            ->join('users', 'users.id_user', '=', 'user_maincity.fk_user')
            ->join('maincity', 'maincity.id_maincity', '=', 'user_maincity.fk_maincity');
    }
}
