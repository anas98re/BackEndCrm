<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subcategory_ticket_fk extends Model
{
    use HasFactory, Loggable;

    protected $table = 'subcategories_ticket_fks';
    public $timestamps = false;

    protected $fillable = [
        'fk_subcategory',
        'fk_ticket'
    ];

    public function subcategories_ticket()
    {
        return $this->belongsTo(subcategorie_ticket::class, 'fk_subcategory', 'id');
    }
}
