<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ticket_detail extends Model
{
    use HasFactory, Loggable;

    protected $table = 'tickets_detail';
    public $timestamps = false;
    protected $primaryKey = 'id_ticket_detail';

    protected $fillable = [
        'id_ticket_detail',
        'fk_ticket',
        'fk_state',
        'tag',
        'notes',
        'fk_user',
        'date_state'
    ];
}
