<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class clients_date extends Model
{
    use HasFactory, Loggable;

    protected $table = 'clients_date';
    public $timestamps = false;
    protected $primaryKey = 'idclients_date';

    protected $fillable = [
        'idclients_date',
        'date_client_visit',
        'fk_user',
        'fk_user_add',
        'is_done',
        'date_done',
        'fk_client',
        'fk_invoice',
        'type_date',
        'processReason',
        'fk_user_update',
        'fk_agent',
        'date_end',
        'fk_user_done',
        'date_update_visit'
    ];

    public function client()
    {
        return $this->belongsTo(clients::class, 'fk_client', 'id_clients');
    }

    public function user()
    {
        return $this->belongsTo(users::class, 'fk_user', 'id_user');
    }

    public function userAdd()
    {
        return $this->belongsTo(users::class, 'fk_user_add', 'id_user');
    }

    public function userUpdate()
    {
        return $this->belongsTo(users::class, 'fk_user_update', 'id_user');
    }

    public function invoice()
    {
        return $this->belongsTo(client_invoice::class, 'fk_invoice', 'id_invoice');
    }

    public function agent()
    {
        return $this->belongsTo(agent::class, 'fk_agent', 'id_agent');
    }

}
