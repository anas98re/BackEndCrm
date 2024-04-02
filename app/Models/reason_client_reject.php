<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reason_client_reject extends Model
{
    use HasFactory, Loggable;

    protected $table = 'reason_client_reject';
    public $timestamps = false;
    protected $primaryKey = 'id_rejectClient';

    protected $fillable = [
        'id_rejectClient',
        'NameReason_reject'
    ];
}
