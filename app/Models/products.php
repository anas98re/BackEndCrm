<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class products extends Model
{
    use HasFactory, Loggable;

    protected $primaryKey = 'id_product';

    protected $table = 'products';
    public $timestamps = false;

    protected $fillable = [
        'id_product',
        'nameProduct',
        'priceProduct',
        'type',
        'fk_country',
        'fk_config',
        'idprd',
        'created_at',
        'fkusercreate',
        'updated_at',
        'fkuserupdate',
        'type_prod_renew'
    ];

    public function user()
    {
        return $this->belongsTo(users::class, 'fkuser', 'id_user');
    }
}
