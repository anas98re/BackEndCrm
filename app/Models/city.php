<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class city extends Model
{
    use HasFactory, Loggable;

    protected $primaryKey = 'id_city';
    protected $table = 'city';
    public $timestamps = false;

    protected $fillable = [
        'id_city',
        'name_city',
        'fk_maincity',
        'mainc',
    ];

}
