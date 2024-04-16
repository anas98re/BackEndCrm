<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class client_communication extends Model
{
    use HasFactory, Loggable;

    protected $table = 'client_communication';
    protected $primaryKey = 'id_communication';
    public $timestamps = false;

    protected $fillable = [
        'id_communication',
        'fk_client',
        'fk_user',
        'date_communication',
        'result',
        'notes',
        'rate',
        'type_communcation',
        'number_wrong',
        'client_repeat',
        'date_next',
        'id_invoice',
        'IDcustomer',
        'user_do',
        'name_enterprisecom',
        'address',
        'type_install',
        'date_last_com_install',
        'client_out',
        'school',
        'is_suspend',
        'isRecommendation',
        'is_visit',
        'user_update',
    ];

    public function client()
    {
        return $this->belongsTo(clients::class, 'fk_client', 'id_clients');
    }

    public function user()
    {
        return $this->belongsTo(users::class, 'fk_user', 'id_user');
    }

    public function invoice()
    {
        return $this->belongsTo(client_invoice::class, 'id_invoice', 'id_invoice');
    }
}
