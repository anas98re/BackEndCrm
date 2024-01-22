<?php

namespace Modules\MobileApp\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\MobileApp\Entities\activity_type;

class ActivityTypeController extends Controller
{
    public function getAll()
    {
        return activity_type::where('id_activity_type',1)->first();
    }

}
