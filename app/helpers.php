<?php


namespace App;

use Spatie\Activitylog\Models\Activity;

class helpers
{
    public static function logActivity($logName, $description, $subjectId, $subjectType, $causerId, $causerType, $properties = [])
    {
        $activity = Activity::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_id' => $subjectId,
            'subject_type' => $subjectType,
            'causer_id' => $causerId,
            'causer_type' => $causerType,
            'properties' => $properties,
        ]);

        // You can also add custom attributes to the activity using the `setAttribute` method
        // $activity->setAttribute('custom_attribute', 'custom_value');
        $activity->save();
    }

    // helpers::logActivity(
    //     'client',
    //     'Client record updated',
    //     $id_clients,
    //     'App\Models\Clients',
    //     auth()->user()->id_user,
    //     'App\Models\User',
    //     [
    //         'type_client' => $request->type_client,
    //         'offer_price' => $request->offer_price,
    //         // Add more properties as needed
    //     ]
    // );

    
}
