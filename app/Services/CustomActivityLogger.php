<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\ActivityLogger;

class CustomActivityLogger extends ActivityLogger
{
    protected function getActivityProperties(Model $model): array
    {
        $properties = parent::getActivityProperties($model);

        $request = app('request');

        $properties['route_name'] = $request->route()->getName();
        $properties['method_name'] = $request->getMethod();
        $properties['ip_address'] = $request->ip();

        return $properties;
    }
}
