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
use Intervention\Image\ImageManager;

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

    public function storeFile($file, $folder)
    {
        // Store the file with the modified filename
        $generatedPath = $file->storeAs($folder, $this->handlingFileName($file), 'public');
        return 'storage/'.$generatedPath;
    }

    public function storeThumbnail($file, $folder, $thumbnail_width)
    {
        $generatedPath = ImageManager::imagick()
            ->read(public_path($this->storeFile($file, $folder)))
            ->resize($thumbnail_width, $thumbnail_width)
            ->save()
            ->origin()
            ->filePath();
        // Store the file with the modified filename
        return str($generatedPath)->after('public/');
    }

    public function handlingFileName($file)
    {
        $originalFilename = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();
        $randomNumber = mt_rand(10000, 99999);

        $allowed_extension = collect(["jpg", "png", "gif", "mp3", "pdf", "jpeg", "3gp", "docx", "doc"]);

        if (!$allowed_extension->contains($fileExtension))
            return response()->json(['message' => 'format_error'], 400);

        // Remove the file extension from the original filename
        $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME);

        $modifiedFilename = str_replace(' ', '_', $filenameWithoutExtension) . '_' . $randomNumber;

        // Apply the regular expression to remove special characters
        $modifiedFilename = preg_replace('/[^A-Za-z0-9_.]/', '', $modifiedFilename);

        // Combine the filename and extension
        $generatedFilename = $modifiedFilename . '.' . $fileExtension;

        return $generatedFilename;
    }

    public static function generateThreeLetters()
    {
        $length = 3;
        $randomString = '';
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $letters[random_int(0, strlen($letters) - 1)];
        }
        return $randomString;
    }
}
