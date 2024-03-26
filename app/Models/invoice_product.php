<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class invoice_product extends Model
{
    use HasFactory, LogsActivity, Loggable;
    protected $primaryKey = 'id_invoice_product';

    protected $table = 'invoice_product';
    public $timestamps = false;

    protected $fillable = [
        'id_invoice_product',
        'fk_id_invoice',
        'fk_product',
        'amount',
        'price',
        'taxtotal',
        'rate_admin',
        'rateUser',
        'idinvoice',
        'name_prod'
    ];


    public function getActivitylogOptions(): LogOptions
    {
        $request = app(Request::class);
        $routePattern = $request->route()->uri();
        $ip = $request->ip();
        $user = auth('sanctum')->user();
        $userName = $user ? $user->nameUser : null;
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->useLogName('invoice_product')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "invoice_product created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "invoice_product updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "invoice_product deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "files_invoice action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getQualifiedKeyName()
    {
        return $this->table . '.' . $this->primaryKey;
    }
}
