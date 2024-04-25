<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class regoin extends Model
{
    use HasFactory, Loggable;

    protected $table = 'regoin';
    public $timestamps = false;
    protected $primaryKey = 'id_regoin';

    protected $fillable = [
        'id_regoin',
        'name_regoin',
        'fk_country'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(country::class, 'fk_country', 'id_country');
    }

    public function clientInvoices()
    {
        return $this->hasManyThrough(client_invoice::class, clients::class, 'fk_regoin', 'fk_idClient');
    }
}
