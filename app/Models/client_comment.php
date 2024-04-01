<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class client_comment extends Model
{
    use HasFactory, Loggable;

    protected $table = 'client_comment';
    protected $primaryKey = 'id_comment';
    public $timestamps = false;

    protected $fillable = [
        'id_comment',
        'fk_user',
        'fk_client',
        'content',
        'type_comment',
        'date_comment',
        'IDcustomer',
        'Username',
        'Email',
        'name_enterprise',
    ];

}
