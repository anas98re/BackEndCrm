<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class testt extends Model
{
    use HasFactory, Loggable;

    protected $table = 'testt';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'created_at',
        'email',
        'nameUser'
    ];
}
