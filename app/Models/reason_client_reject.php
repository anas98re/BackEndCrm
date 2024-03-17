<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reason_client_reject extends Model
{
    use HasFactory;

    protected $table = 'reason_client_reject';
    public $timestamps = false;

    protected $fillable = [
        'id_rejectClient',
        'NameReason_reject'
    ];
}
