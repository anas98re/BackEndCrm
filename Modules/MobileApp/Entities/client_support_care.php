<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class client_support_care extends Model
{
    use HasFactory;

    protected $table = 'client_support_care';

    protected $fillable = [
        'id_client_supportCare',
        'fk_idclient',
        'fk_iduser',
        'type_client',
        'notes',
        'date_Assign_toUserSupport'
    ];
}
