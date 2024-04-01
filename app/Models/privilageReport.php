<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class privilageReport extends Model
{
    use HasFactory, Loggable;

    protected $table = 'privilages_report';
    public $timestamps = false;

    protected $fillable = [
        'changes_data',
        'level_name',
        'edit_date',
        'user_update_name',
        'fkuser'
    ];

    public function user()
    {
        return $this->belongsTo(users::class, 'fkuser', 'id_user');
    }
}
