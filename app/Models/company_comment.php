<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class company_comment extends Model
{
    use HasFactory;

    protected $table = 'company_comment';
    public $timestamps = false;
    protected $fillable = [
        'id_comment_company',
        'fk_user',
        'fk_company',
        'content',
        'date_comment'
    ];
}
