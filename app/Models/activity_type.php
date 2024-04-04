<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class activity_type extends Model
{
    use HasFactory, Loggable;

    protected $table = 'activity_type';
    public $timestamps = false;
    protected $primaryKey = 'id_activity_type';

    protected $fillable = [
        'id_activity_type',
        'name_activity_type'
    ];

}
