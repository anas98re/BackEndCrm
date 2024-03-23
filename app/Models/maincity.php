<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class maincity extends Model
{
    use HasFactory, Loggable;

    protected $table = 'maincity';
    protected $primaryKey = 'id_maincity';
    public $timestamps = false;
    protected $fillable = [
        'id_maincity',
        'namemaincity',
        'fk_country'
    ];

    public function cities()
    {
        return $this->hasMany(city::class, 'fk_maincity', 'id_maincity');
    }
}
