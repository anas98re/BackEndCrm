<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class company extends Model
{
    use HasFactory;

    protected $table = 'company';
    public $timestamps = false;
    protected $primaryKey = 'id_Company';
    protected $fillable = [
        'id_Company',
        'name_company',
        'path_logo'
    ];
}
