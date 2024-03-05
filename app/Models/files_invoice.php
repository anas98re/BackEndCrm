<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class files_invoice extends Model
{
    use HasFactory;

    protected $table = 'files_invoice';

    public $timestamps = false;
    protected $fillable = [
        'id',
        'fk_invoice',
        'file_attach_invoice',
        'is_support_employee'
    ];
}
