<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reason_client_reject extends Model
{
    use HasFactory;

    protected $table = 'reason_client_reject';

    protected $fillable = [
        'id_rejectClient',
        'NameReason_reject'
    ];
}
