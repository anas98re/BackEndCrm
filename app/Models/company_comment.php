<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class company_comment extends Model
{
    use HasFactory, Loggable;

    protected $table = 'company_comment';
    public $timestamps = false;
    protected $primaryKey = 'id_comment_company';
    protected $fillable = [
        'id_comment_company',
        'fk_user',
        'fk_company',
        'content',
        'date_comment'
    ];


    public function Users()
    {
        return $this->belongsTo(users::class, 'fk_user', 'id_user')->select('id_user', 'nameUser','img_image');
    }
}
