<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class temp_part extends Model
{
    use HasFactory, Loggable;

    protected $table = 'temp_part';
    public $timestamps = false;

    protected $fillable = [
        'id_temp',
        'fk_part',
        'id_invoice_part',
        'name_part'
    ];
}
