<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientCommentMention extends Model
{
    use HasFactory;

    protected $table = 'client_comment_mentions';
    public $timestamps = false;

    protected $fillable = [
        'comment_id',
        'user_id',
        'content',
        'date_mention',
        'is_read'
    ];
}
