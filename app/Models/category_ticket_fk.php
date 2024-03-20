<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category_ticket_fk extends Model
{
    use HasFactory;

    protected $table = 'categories_ticket_fks';
    public $timestamps = false;

    protected $fillable = [
        'fk_category',
        'fk_ticket'
    ];
}
