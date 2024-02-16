<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class users extends Model
{
    // use HasFactory;
    use HasApiTokens, HasFactory, Notifiable;
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
}
