<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class details_traceinvoice extends Model
{
    use HasFactory, Loggable;

    protected $table = 'details_traceinvoice';
    public $timestamps = false;

    protected $fillable = [
        'id_detail_trace',
        'fk_trace',
        'description'
    ];
}
