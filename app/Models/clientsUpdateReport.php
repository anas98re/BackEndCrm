<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientsUpdateReport extends Model
{
    use HasFactory;

    protected $table = 'clients_update_reports';
    public $timestamps = false;

    protected $fillable = [
        'changesData',
        'edit_date',
        'fk_user'
    ];
}
