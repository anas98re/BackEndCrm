<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class files_invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'fk_invoice',
        'file_attach_invoice'
    ];
}
