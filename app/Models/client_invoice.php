<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class client_invoice extends Model
{
    use HasFactory, LogsActivity, Loggable;

    protected $primaryKey = 'id_invoice';

    protected $table = 'client_invoice';
    public $timestamps = false;

    protected $fillable = [
        'id_invoice',
        'date_create',
        'type_pay',
        'renew_year',
        'type_installation',
        'image_record',
        'fk_idClient',
        'fk_idUser',
        'amount_paid',
        'notes',
        'total',
        'lastuserupdate',
        'dateinstall_done',
        'isdoneinstall',
        'userinstall',
        'dateinstall_task',
        'fkusertask',
        'date_lastuserupdate',
        'reason_date',
        'stateclient',
        'value_back',
        'desc_reason_back',
        'reason_back',
        'fkuser_back',
        'date_change_back',
        'daterepaly',
        'fkuserdatareplay',
        'iduser_approve',
        'isApprove',
        'date_approve',
        'numbarnch',
        'nummostda',
        'numusers',
        'numTax',
        'imagelogo',
        'clientusername',
        'address_invoice',
        'emailuserinv',
        'nameuserinv',
        'IDcustomer',
        'isdelete',
        'date_delete',
        'user_delete',
        'name_enterpriseinv',
        'ready_install',
        'user_ready_install',
        'date_readyinstall',
        'user_not_ready_install',
        'date_not_readyinstall',
        'count_delay_ready',
        'isApproveFinance',
        'iduser_FApprove',
        'Date_FApprove',
        'renew2year',
        'participate_fk',
        'rate_participate',
        'type_back',
        'fk_regoin_invoice',
        'type_seller',
        'fk_agent',
        'currency_name',
        'renew_pluse',
        'payment_idAdd',
        'payment_date',
        'file_attach',
        'renew_agent',
        'file_reject',
        'approve_back_done',
        'TypeReadyClient',
        'notes_ready',
        'reason_suspend'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $request = app(Request::class);
        $routePattern = $request->route()->uri();
        $ip = $request->ip();
        $user = auth('sanctum')->user();
        $userName = $user ? $user->nameUser : null;
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->useLogName('client_invoice Log')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "client_invoice created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "client_invoice updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "client_invoice deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "client_invoice action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }
}
