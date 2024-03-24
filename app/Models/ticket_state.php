<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ticket_state extends Model
{
    use HasFactory, Loggable;

    protected $table = 'ticket_state';
    public $timestamps = false;

    protected $fillable = [
        'name_state'
    ];
}
