<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class subcategorie_ticket extends Model
{
    use HasFactory, Loggable;

    protected $table = 'subcategories_ticket';
    public $timestamps = false;

    protected $fillable = [
        'sub_category_ar',
        'sub_category_en',
        'classification'
    ];

}
