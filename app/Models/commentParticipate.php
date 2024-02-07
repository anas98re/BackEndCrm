<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class commentParticipate extends Model
{
    use HasFactory;

    protected $table = 'comment_participates';
    public $timestamps = false;

    protected $fillable = [
        'participate_id',
        'content',
        'date_comment'
    ];

    public function Participates()
    {
        return $this->belongsTo(participate::class, 'participate_id', 'id_participate');
    }
}
