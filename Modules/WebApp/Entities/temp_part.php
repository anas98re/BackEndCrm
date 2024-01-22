<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class temp_part extends Model
{
    use HasFactory;

    protected $table = 'temp_part';

    protected $fillable = [
        'id_temp',
        'fk_part',
        'id_invoice_part',
        'name_part'
    ];
}
