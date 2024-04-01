<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;


class company extends Model
{
    use HasFactory, Loggable;

    protected $table = 'company';
    public $timestamps = false;
    protected $primaryKey = 'id_Company';

    protected $fillable = [
        'id_Company',
        'name_company',
        'path_logo'
    ];


}
