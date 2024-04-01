<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class commentParticipate extends Model
{
    use HasFactory, Loggable;

    protected $table = 'comment_participates';
    public $timestamps = false;

    protected $fillable = [
        'participate_id',
        'content',
        'date_comment',
        'user_id'
    ];


    public function Participates()
    {
        return $this->belongsTo(participate::class, 'participate_id', 'id_participate');
    }
    public function users()
    {
        return $this->belongsTo(users::class, 'user_id', 'id_user');
    }
}
