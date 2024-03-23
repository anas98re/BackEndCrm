<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class categorie_tiket extends Model
{
    use HasFactory, Loggable;

    protected $table = 'categories_ticket';
    public $timestamps = false;

    protected $fillable = [
        'category_ar',
        'category_en',
        'classification'
    ];

}
