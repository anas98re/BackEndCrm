<?php
namespace App\Traits;

use App\Models\ChangeLog;
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
            // Retrieve the original attributes
            $originalAttributes = $model->getOriginal();

            // Get the updated attributes
            $updatedAttributes = $model->getDirty();

            // Filter the modified attributes
            $modifiedAttributesBefore = array_intersect_key($originalAttributes, $updatedAttributes);
            $modifiedAttributesAfter = array_intersect_key($updatedAttributes, $originalAttributes);

            // Log the update with the modified data
            ChangeLog::create([
                'model' => get_class($model),
                'action' => 'updated',
                'old_data' => json_encode($modifiedAttributesBefore, JSON_UNESCAPED_UNICODE),
                'new_data' => json_encode($modifiedAttributesAfter, JSON_UNESCAPED_UNICODE),
                'description' => get_class($model) . ' updated by ' . $userName . ', using route: ' . $routeName . ' from IP: ' . $ip,
                'user_id' => $userId,
                'model_id' => $model->getKey(),
                'route' => $routeName,
                'ip' => $ip
            ]);
        });

        static::deleting(function ($model) use ($user, $routeName, $ip, $userName, $userId) {
            // Log the deletion
            ChangeLog::create([
                'model' => get_class($model),
                'action' => 'deleted',
                'old_data' => json_encode($model->getOriginal(), JSON_UNESCAPED_UNICODE),
                'new_data' => null,
                'description' => get_class($model) . ' deleted by ' . $userName . ', using route: ' . $routeName . ' from IP: ' . $ip,
                'user_id' => $userId,
                'model_id' => $model->getKey(),
                'route' => $routeName,
                'ip' => $ip
            ]);
        });
    }
}
