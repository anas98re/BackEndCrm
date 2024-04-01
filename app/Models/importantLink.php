<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class importantLink extends Model
{
    use HasFactory, Loggable;
    public $timestamps = false;

    protected $fillable = [
        'title',
        'address',
        'link',
        'notes',
        'add_date',
        'edit_date',
        'user_id',
        'clause',
        'department'
    ];

}
