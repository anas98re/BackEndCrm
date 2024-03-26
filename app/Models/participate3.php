<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class participate3 extends Model
{
    use HasFactory, Loggable;

    protected $table = 'participate3';
    public $timestamps = false;

    protected $fillable = [
        'id_participate',
        'name_participate',
        'mobile_participate',
        'namebank_participate',
        'numberbank_participate'
    ];
}
