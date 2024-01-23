<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class importantLink extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'title',
        'address',
        'link',
        'notes',
        'add_date',
        'edit_date',
        'user_id',
    ];
}
