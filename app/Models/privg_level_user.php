<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class privg_level_user extends Model
{
    use HasFactory, Loggable;

    protected $table = 'privg_level_user';
    protected $primaryKey = 'id_privg_user';
    public $timestamps = false;

    protected $fillable = [
        'id_privg_user',
        'fk_level',
        'fk_privileg',
        'is_check'
    ];


}
