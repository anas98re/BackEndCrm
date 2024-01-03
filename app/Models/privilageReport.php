<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class privilageReport extends Model
{
    use HasFactory;

    protected $table = 'Privilages_report';

    protected $fillable = [
        'privilage_name',
        'edit_date',
        'user_update_name',
        'fkuser'
    ];
}
