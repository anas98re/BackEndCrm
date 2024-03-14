<?php

namespace App\Extensions;

use Illuminate\Http\Request;
use Spatie\Activitylog\ActivityLogger as BaseActivityLogger;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;

class ActivityLogger extends BaseActivityLogger
{
    protected function getActivity(): ActivityContract
    {
        $activity = parent::getActivity();

        $request = app(Request::class);

        $properties = [
            'route_name' => $request->route()->getName(),
            'method_name' => $request->getMethod(),
            'ip_address' => $request->ip(),
        ];

        $activity->properties = $activity->properties->merge($properties);

        return $activity;
    }
}
