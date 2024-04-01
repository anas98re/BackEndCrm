<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class clients extends Model
{
    use HasFactory, Loggable;

    protected $table = 'clients';
    protected $primaryKey = 'id_clients';

    public $timestamps = false;
    protected $fillable = [
        'id_clients', 'name_client', 'name_enterprise', 'type_job',
        'city', 'location', 'fk_regoin', 'date_create', 'type_client',
        'fk_user', 'date_transfer', 'fkusertrasfer', 'mobile', 'date_changetype',
        'reason_change', 'reason_transfer', 'offer_price', 'date_price', 'user_do',
        'ismarketing', 'address_client', 'date_recive', 'userAdd_email', 'phone',
        'IDcustomer', 'descActivController', 'presystem', 'sourcclient',
        'activity_type_fk', 'user_add', 'date_visit_Client', 'done_transfer',
        'done_visit', 'tag', 'size_activity', 'fk_client_source', 'email',
        'fk_rejectClient', 'SerialNumber', 'is_comments_check','type_record',
        'reason_class','type_classification','date_update', 'fkuser_update','received_date',
        'approveIduser_reject','date_reject','fk_user_reject','date_approve_reject'
    ];


}
