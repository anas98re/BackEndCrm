<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reasonsofout extends Model
{
    use HasFactory, Loggable;

    protected $table = 'reasonsofout';
    public $timestamps = false;

    protected $fillable = [
        'id_reason',
        'name_reason',
        'type'
    ];
}
