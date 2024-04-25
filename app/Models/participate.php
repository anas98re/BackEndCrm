<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class participate extends Model
{
    use HasFactory, Loggable;

    protected $primaryKey = 'id_participate';

    protected $table = 'participate';
    public $timestamps = false;

    protected $fillable = [
        'id_participate',
        'name_participate',
        'mobile_participate',
        'namebank_participate',
        'numberbank_participate',
        'add_date',
        'update_date',
        'fk_user_add',
        'fk_user_update',
        'fk_city',
    ];

    public function clientInvoices()
    {
        return $this->hasMany(client_invoice::class, 'fk_idUser');
    }

}
