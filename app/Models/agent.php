<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class agent extends Model
{
    use HasFactory, Loggable;

    protected $table = 'agent';
    public $timestamps = false;
    protected $primaryKey = 'id_agent';

    protected $fillable = [
        'id_agent',
        'name_agent',
        'type_agent',
        'email_egent',
        'mobile_agent',
        'fk_country',
        'description',
        'image_agent',
        'cityId',
        'add_date',
        'update_date',
        'fk_user_add',
        'fk_user_update',
        'fkuser_training',
        'is_training',
        'date_training',
    ];

    public function clientInvoices()
    {
        return $this->hasMany(client_invoice::class, 'fk_idUser');
    }

}


