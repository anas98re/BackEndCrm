<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reasonsofout extends Model
{
    use HasFactory;

    protected $table = 'reasonsofout';

    protected $fillable = [
        'id_reason',
        'name_reason',
        'type'
    ];
}
