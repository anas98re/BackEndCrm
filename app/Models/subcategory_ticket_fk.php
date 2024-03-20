<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subcategory_ticket_fk extends Model
{
    use HasFactory;

    protected $table = 'subcategories_ticket_fks';
    public $timestamps = false;

    protected $fillable = [
        'fk_subcategory',
        'fk_ticket'
    ];
}
