<?php

namespace Modules\MobileApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class details_traceinvoice extends Model
{
    use HasFactory;

    protected $table = 'details_traceinvoice';

    protected $fillable = [
        'id_detail_trace',
        'fk_trace',
        'description'
    ];
}
