<?php

namespace Modules\WebApp\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class testt extends Model
{
    use HasFactory;

    protected $table = 'testt';

    protected $fillable = [
        'id',
        'created_at',
        'email',
        'nameUser'
    ];
}
