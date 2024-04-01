<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class approveclient extends Model
{
    use HasFactory, Loggable;

    protected $primaryKey = 'id_approveClient';
    protected $table = 'approveclient';
    public $timestamps = false;


    protected $fillable = [
        'id_approveClient',
        'fk_user',
        'fk_client',
        'date_approve',
        'is_approve',
        'fk_invoice',
    ];

}
