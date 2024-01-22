<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class client_comment extends Model
{
    use HasFactory;

    protected $table = 'client_comment';

    protected $fillable = [
        'id_comment',
        'fk_user',
        'fk_client',
        'content',
        'date_comment',
        'IDcustomer',
        'Username',
        'Email',
        'name_enterprise',
    ];
}
