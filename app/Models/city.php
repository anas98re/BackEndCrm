<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class city extends Model
{
    use HasFactory, Loggable;

    protected $primaryKey = 'id_city';
    protected $table = 'city';
    public $timestamps = false;

    protected $fillable = [
        'id_city',
        'name_city',
        'fk_maincity',
        'mainc',
    ];


    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }

    public function mainCity(): BelongsTo
    {
        return $this->belongsTo(maincity::class, 'fk_maincity', 'id_maincity');
    }

    public function regoin()
    {
        return $this->hasMany(regoin::class, 'id_regoin');
    }
}
