<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class config extends Model
{
    use HasFactory;

    protected $table = 'config';
    public $timestamps = false;

    protected $fillable = [
        'idconfig',
        'val_target',
        'val_taxrate_user',
        'val_taxrate_admin',
        'val_rate_user',
        'fk_country'
    ];
}
