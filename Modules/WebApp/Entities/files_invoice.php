<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class files_invoice extends Model
{
    use HasFactory;

    protected $table = 'files_invoice';

    protected $fillable = [
        'id',
        'fk_invoice',
        'file_attach_invoice'
    ];
}
