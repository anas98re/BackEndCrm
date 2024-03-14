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

class AppSrevices extends JsonResponeService
{
    public function handlingfileInvoiceName($file)
    {
        $originalFilename = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();
        $randomNumber = mt_rand(10000, 99999);

        // Remove the file extension from the original filename
        $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME);

        $modifiedFilename = str_replace(' ', '_', $filenameWithoutExtension) . '_' . $randomNumber;

        // Apply the regular expression to remove special characters
        $modifiedFilename = preg_replace('/[^A-Za-z0-9_.]/', '', $modifiedFilename);

        // Combine the filename and extension
        $generatedFilename = $modifiedFilename . '.' . $fileExtension;

        // Store the file with the modified filename
        $generatedPath = $file->storeAs('invoiceFiles', $generatedFilename, 'public');
        return $generatedPath;
    }
}
