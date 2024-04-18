<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class files_invoice extends Model
{
    use HasFactory, Loggable;

    protected $table = 'files_invoice';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'fk_invoice',
        'file_attach_invoice',
        'type_file',
        'add_date'
    ];

}
