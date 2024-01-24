<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class convertClintsStaticts extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'numberOfClients',
        'convert_date',
        'oldUserId',
        'newUserId',
        'description',
    ];
}
