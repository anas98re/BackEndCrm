<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'reason_change', 'reason_transfer', 'offer_price', 'date_price', 'date_price2', 'user_do',
        'ismarketing', 'address_client', 'date_recive', 'userAdd_email', 'phone',
        'IDcustomer', 'descActivController', 'presystem', 'sourcclient',
        'activity_type_fk', 'user_add', 'date_visit_Client', 'done_transfer',
        'done_visit', 'tag', 'size_activity', 'fk_client_source', 'email',
        'fk_rejectClient', 'SerialNumber', 'is_comments_check','type_record',
        'reason_class','type_classification','date_update', 'fkuser_update','received_date',
        'approveIduser_reject','date_reject','fk_user_reject','date_approve_reject'
    ];


    public function transferTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reason_transfer', 'id_user');
    }

    public function clientSource(): BelongsTo
    {
        return $this->belongsTo(self::class, 'fk_client_source', 'id_clients');
    }

    public function regoin(): BelongsTo
    {
        return $this->belongsTo(regoin::class, 'fk_regoin', 'id_regoin');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_user', 'id_user');
    }

    public function userDo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_do', 'id_user');
    }

    public function userTransfer(): BelongsTo
    {
        return $this->belongsTo(users::class, 'fkusertrasfer', 'id_user');
    }

    public function cityRelation(): BelongsTo
    {
        return $this->belongsTo(city::class, 'city', 'id_city');
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(activity_type::class, 'activity_type_fk', 'id_activity_type');
    }

    public function preSystemRelation(): BelongsTo
    {
        return $this->belongsTo(company::class, 'presystem', 'id_Company');
    }

    public function userAdd(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_add', 'id_user');
    }

    public function reasonReject(): BelongsTo
    {
        return $this->belongsTo(reason_client_reject::class, 'fk_rejectClient', 'id_rejectClient');
    }

    public function clientInvoices()
    {
        return $this->hasMany(client_invoice::class, 'fk_idUser');
    }

    public function city()
    {
        return $this->belongsTo(city::class, 'city');
    }

    public function scopeFilter($query, array $filters)
    {
        $filters['filter'] = $filters['filter'] == 'null' || $filters['filter'] == null ? false: $filters['filter'];
        $filters['fk_regoin_prv'] = $filters['fk_regoin_prv'] == 'null' || $filters['fk_regoin_prv'] == null ? false: $filters['fk_regoin_prv'];
        $filters['fk_country'] = $filters['fk_country'] == 'null' || $filters['fk_country'] == null ? false: $filters['fk_country'];
        $filters['fk_regoin'] = $filters['fk_regoin'] == 'null' || $filters['fk_regoin'] == null ? false: $filters['fk_regoin'];
        $filters['type_client'] = $filters['type_client'] == 'null' || $filters['type_client'] == null ? false: $filters['type_client'];
        $filters['type_record'] = $filters['type_record'] == 'null' || $filters['type_record'] == null ? false: $filters['type_record'];
        $filters['fk_user_prv'] = $filters['fk_user_prv'] == 'null' || $filters['fk_user_prv'] == null ? false: $filters['fk_user_prv'];
        $filters['fk_user'] = $filters['fk_user'] == 'null' || $filters['fk_user'] == null ? false: $filters['fk_user'];
        $filters['activity_type_fk'] = $filters['activity_type_fk'] == 'null' || $filters['activity_type_fk'] == null ? false: $filters['activity_type_fk'];

        $query->when($filters['filter'] ?? false, fn($query, $search) =>
            $query->where(fn($query) =>
                $query->where('name_enterprise', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%')
                    ->orWhere('name_client', 'like', '%' . $search . '%')
                    ->orWhere('SerialNumber', 'like', '%' . $search . '%')
            )
        );

        $query->when(($filters['fk_regoin_prv'] ?? false) && (is_null($filters['fk_user_prv'] ?? null)), fn($query, $filter) =>
                $query->where('fk_regoin', $filter)
        );

        $query->when((($filters['fk_country'] ?? false) && (is_null($filters['fk_user_prv'] ?? null))), fn($query, $filter) =>
                $query->whereHas('regoin', fn($query) =>
                    $query->where('fk_country', $filter)
                )
        );

        $query->when($filters['fk_regoin'] ?? false, fn($query, $filter) =>
                $query->where('fk_regoin', $filter)
        );

        $query->when($filters['type_client'] ?? false, fn($query, $filter) =>
                $query->where('type_client', $filter)
        );

        $query->when($filters['type_record'] ?? false, fn($query, $filter) =>
                $query->where('type_record', $filter)
        );

        $query->when($filters['fk_user_prv'] ?? false, function ($query, $filter) use ($filters) {
            if (!is_null($filters['fk_regoin_prv'] ?? null))
                $query->where(function ($query) use ($filter, $filters) {
                    $query->where('fk_user', $filter)
                        ->orWhere('fk_regoin', $filters['fk_regoin_prv']);
                });
            else
                $query->where(function ($query) use ($filter, $filters) {
                    $query->where('fk_user', $filter)
                        ->whereHas('regoin', fn($query) =>
                            $query->where('fk_country', $filter)
                        );
                });
        });

        $query->when($filters['fk_user'] ?? false, fn($query, $filter) =>
                $query->where('fk_user', $filter)
        );

        $query->when($filters['activity_type_fk'] ?? false, fn($query, $filter) =>
                $query->where('activity_type_fk', $filter)
        );

    }

}
