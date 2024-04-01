<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class temp_client_transfer extends Model
{
    use HasFactory, Loggable;

    protected $table = 'temp_client_transfer';
    public $timestamps = false;

    protected $fillable = [
        'id_temp_client_transfer',
        'fk_usertransfer',
        'fk_userr',
        'nameprise',
        'date_tr'
    ];
}
