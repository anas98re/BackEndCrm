<?php

namespace App\Services;

use App\Constants;
use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\clients;
use App\Models\notifiaction;
use App\Models\regoin;
use App\Models\tsks_group;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CompanySrevices extends JsonResponeService
{
    public function handlingImageName($path_logo)
    {
        $originalFilename = $path_logo->getClientOriginalName();
        $fileExtension = $path_logo->getClientOriginalExtension();
        $randomNumber = mt_rand(10000, 99999);

        // Remove the file extension from the original filename
        $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME);

        $modifiedFilename = str_replace(' ', '_', $filenameWithoutExtension) . '_' . $randomNumber;

        // Combine the filename and extension
        $generatedFilename = $modifiedFilename . '.' . $fileExtension;

        // Store the file with the modified filename
        $generatedPath = $path_logo->storeAs('companiesLogo', $generatedFilename);
        return $generatedPath;
    }
}
