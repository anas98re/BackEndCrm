<?php

namespace App\Traits;

use App\Models\ChangeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

trait Loggable
{
    protected static function bootLoggable()
    {
        $request = app(Request::class);
        $routeName = $request && $request->route() ? $request->route()->uri() : null;
        $ip = $request->ip();
        $user = auth('sanctum')->user();
        $userName = $user ? $user->nameUser : null;
        $userId = $user ? $user->id_user : null;

        static::updating(function ($model) use ($user, $routeName, $ip, $userName, $userId) {

            $originalAttributes = $model->getOriginal();
            $updatedAttributes = $model->getDirty();

            $modifiedAttributes = [];

            foreach ($updatedAttributes as $attribute => $newValue) {
                $oldValue = $originalAttributes[$attribute] ?? null;

                if ($oldValue !== $newValue) {
                    $modifiedAttributes[$attribute] = " ($oldValue) TO ($newValue)";
                }
            }
            ChangeLog::create([
                'model' => get_class($model),
                'action' => 'updated',
                'changesData' => json_encode($modifiedAttributes, JSON_UNESCAPED_UNICODE),
                'description' => get_class($model) . ' updated by ' . $userName . ', using route: ' . $routeName . ' from IP: ' . $ip,
                'user_id' => $userId,
                'model_id' => $model->getKey(),
                'edit_date' =>  Carbon::now('Asia/Riyadh')->toDateTimeString(),
                'route' => $routeName,
                'ip' => $ip
            ]);
        });

        static::deleting(function ($model) use ($user, $routeName, $ip, $userName, $userId) {
            ChangeLog::create([
                'model' => get_class($model),
                'action' => 'deleted',
                'changesData' => json_encode($model->getOriginal(), JSON_UNESCAPED_UNICODE),
                'description' => get_class($model) . ' deleted by ' . $userName . ', using route: ' . $routeName . ' from IP: ' . $ip,
                'user_id' => $userId,
                'model_id' => $model->getKey(),
                'edit_date' => Carbon::now('Asia/Riyadh')->toDateTimeString(),
                'route' => $routeName,
                'ip' => $ip
            ]);
        });
    }
}




// Previos way
       // static::updating(function ($model) use ($user, $routeName, $ip, $userName, $userId) {
        //     $originalAttributes = $model->getOriginal();

        //     $updatedAttributes = $model->getDirty();

        //     // Filter the modified attributes
        //     $modifiedAttributesBefore = array_intersect_key($originalAttributes, $updatedAttributes);
        //     $modifiedAttributesAfter = array_intersect_key($updatedAttributes, $originalAttributes);

        //     ChangeLog::create([
        //         'model' => get_class($model),
        //         'action' => 'updated',
        //         'old_data' => json_encode($modifiedAttributesBefore, JSON_UNESCAPED_UNICODE),
        //         'new_data' => json_encode($modifiedAttributesAfter, JSON_UNESCAPED_UNICODE),
        //         'description' => get_class($model) . ' updated by ' . $userName . ', using route: ' . $routeName . ' from IP: ' . $ip,
        //         'user_id' => $userId,
        //         'model_id' => $model->getKey(),
        //         'route' => $routeName,
        //         'ip' => $ip
        //     ]);
        // });
