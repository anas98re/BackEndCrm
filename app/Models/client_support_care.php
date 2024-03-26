<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class client_support_care extends Model
{
    use HasFactory, Loggable;

    protected $table = 'client_support_care';
    public $timestamps = false;

    protected $fillable = [
        'id_client_supportCare',
        'fk_idclient',
        'fk_iduser',
        'type_client',
        'notes',
        'date_Assign_toUserSupport'
    ];
}
