<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class privilageReport extends Model
{
    use HasFactory;

    protected $table = 'privilages_report';

    protected $fillable = [
        'changes_data',
        'level_name',
        'edit_date',
        'user_update_name',
        'fkuser'
    ];
}
