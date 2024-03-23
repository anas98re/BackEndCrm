<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class convertClintsStaticts extends Model
{
    use HasFactory, Loggable;

    public $timestamps = false;
    protected $fillable = [
        'numberOfClients',
        'convert_date',
        'oldUserId',
        'newUserId',
        'description',
    ];

    public function oldUser()
    {
        return $this->belongsTo(User::class, 'oldUserId', 'id_user');
    }

    public function newUser()
    {
        return $this->belongsTo(User::class, 'newUserId', 'id_user');
    }
}
