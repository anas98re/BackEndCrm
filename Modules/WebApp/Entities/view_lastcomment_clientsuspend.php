<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class view_lastcomment_clientsuspend extends Model
{
    use HasFactory;

    protected $table = 'view_lastcomment_clientsuspend';

    protected $fillable = [
        'id_clients',
        'date_create',
        'content',
        'name_enterprise',
        'date_approve',
        'name_city',
        'namemaincity',
        'nameUser',
        'dateCommentClient',
        'nameuser_comment',
        'name_regoin',
        'type_install',
        'sourcclient',
        'type_invoice',
        'type_client'
    ];
}
