<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Illuminate\Http\Request;

class levelModel extends Model
{
    use HasFactory, Loggable;
    use HasFactory, Loggable;

    protected $table = 'level';
    public $timestamps = false;

    protected $fillable = [
        'id_level',
        'name_level',
        'periorty'
    ];

}
