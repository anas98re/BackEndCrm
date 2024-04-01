<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class attachment extends Model
{
    use HasFactory, Loggable;

    protected $table = 'task_attachments';

    protected $fillable = [
        'create_date',
        'file_path',
        'task_id',
        'created_by'
    ];

}
