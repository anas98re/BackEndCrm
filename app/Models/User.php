<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';

    protected $primaryKey = 'id_user';
    
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    
}
