<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifiaction extends Model
{
    use HasFactory;

    protected $table = 'notifiaction';
    public $timestamps = false;

    protected $fillable = [
        'id_notify',
        'message',
        'from_user',
        'to_user',
        'type_notify',
        'isread',
        'data',
        'dateNotify'
    ];
}
