<?php

namespace App\Models;

use App\Traits\Loggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\BelongsToManyRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class client_invoice extends Model
{
    use HasFactory, Loggable;

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
        'reason_suspend',
        'reason_notReady',
        'date_back_now',
        'invoice_source',
        'date_updatePayment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(users::class, 'fk_idUser');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(clients::class, 'fk_idClient');
    }

    public function regoin(): BelongsTo
    {
        return $this->belongsTo(regoin::class, 'fk_regoin_invoice');
    }

    public function userUpdated(): BelongsTo
    {
        return $this->belongsTo(users::class, 'lastuserupdate');
    }

    public function userInstalled(): BelongsTo
    {
        return $this->belongsTo(users::class, 'userinstall');
    }

    public function userApproved(): BelongsTo
    {
        return $this->belongsTo(users::class, 'iduser_approve');
    }

    public function userBack(): BelongsTo
    {
        return $this->belongsTo(users::class, 'fkuser_back');
    }

    public function userReplay(): BelongsTo
    {
        return $this->belongsTo(users::class, 'fkuserdatareplay');
    }

    public function userTask(): BelongsTo
    {
        return $this->belongsTo(users::class, 'fkusertask');
    }

    public function userReadyInstall(): BelongsTo
    {
        return $this->belongsTo(users::class, 'user_ready_install');
    }

    public function userNotReadyInstall(): BelongsTo
    {
        return $this->belongsTo(users::class, 'user_not_ready_install');
    }

    public function participate(): BelongsTo
    {
        return $this->belongsTo(participate::class, 'participate_fk');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(agent::class, 'fk_agent');
    }

    public function files(): HasMany
    {
        return $this->hasMany(files_invoice::class, 'fk_invoice');
    }

    public function regoinInvoice()
    {
        return $this->belongsTo(regoin::class, 'fk_regoin_invoice', 'id_regoin');
    }

    public function invoiceProducts()
    {
        return $this->hasMany(invoice_product::class, 'fk_id_invoice');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(products::class, invoice_product::class, 'fk_id_invoice', 'fk_product')->withPivot([
            'id_invoice_product' => (string) 'id_invoice_product',
            'fk_id_invoice' => 'fk_id_invoice',
            'fk_product' => (string)'fk_product',
            'amount' => 'amount',
            'price' => 'price',
            'taxtotal' => 'taxtotal',
            'rate_admin' => 'rate_admin',
            'rateUser' => 'rateUser',
            'idinvoice' => 'idinvoice',
            'name_prod' => 'name_prod'
        ]);
    }

    public function scopeFilter($query, array $filters)
    {
        $filters['type_seller'] = $filters['type_seller'] == 'null' || $filters['type_seller'] == null ? false: $filters['type_seller'];
        $filters['fk_regoin_invoice'] = $filters['fk_regoin_invoice'] == 'null' || $filters['fk_regoin_invoice'] == null ? false: $filters['fk_regoin_invoice'];
        $filters['TypeReadyClient'] = $filters['TypeReadyClient'] == 'null' || $filters['TypeReadyClient'] == null ? false: $filters['TypeReadyClient'];
        $filters['from'] = $filters['from'] == 'null' || $filters['from'] == null ? false: $filters['from'];
        $filters['to'] = $filters['to'] == 'null' || $filters['to'] == null ? false: $filters['to'];
        $filters['search_query'] = $filters['search_query'] == 'null' || $filters['search_query'] == null ? false: $filters['search_query'];
        $filters['fk_agent'] = $filters['fk_agent'] == 'null' || $filters['fk_agent'] == null ? false: $filters['fk_agent'];
        $filters['participate_fk'] = $filters['participate_fk'] == 'null' || $filters['participate_fk'] == null ? false: $filters['participate_fk'];
        $filters['fk_idUser'] = $filters['fk_idUser'] == 'null' || $filters['fk_idUser'] == null ? false: $filters['fk_idUser'];

        $query->when(
            $filters['search_query'] ?? false,
            fn($query, $search) =>
            $query->where(
                fn($query) =>
                $query->whereHas('client', function ($query) use ($search) {
                    $query->where('name_enterprise', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%');
                })
                ->orWhereHas('regoin', function ($query) use ($search) {
                    $query->where('name_regoin', 'like', '%' . $search . '%');
                })
            )
        );

        $query->when($filters['type_seller'] ?? false, fn($query, $filter) =>
            $query->where('type_seller', $filter)
        );

        $query->when($filters['fk_regoin_invoice'] ?? false, fn($query, $filter) =>
            $query->where('fk_regoin_invoice', $filter)
        );

        $query->when($filters['TypeReadyClient'] ?? false, fn($query, $filter) =>
            $query->where('TypeReadyClient', $filter)
        );

        $query->when($filters['fk_agent'] ?? false, fn($query, $filter) =>
            $query->where('fk_agent', $filter)
        );

        $query->when($filters['participate_fk'] ?? false, fn($query, $filter) =>
            $query->where('participate_fk', $filter)
        );

        $query->when($filters['fk_idUser'] ?? false, fn($query, $filter) =>
            $query->where('fk_idUser', $filter)
        );

        $query->when($filters['from'] ?? false, fn($query, $filter) =>
            $query->whereBetween('date_approve', [$filters['from'], $filters['to'] == false? Carbon::now(): $filters['to']])
        );

    }

}
