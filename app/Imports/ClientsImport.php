<?php

namespace App\Imports;

use App\Models\clients;
use App\Models\importantLink;
use App\Models\users;
use Carbon\Carbon;
use DateTime;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClientsImport implements ToModel
{

    public function model(array $row)
    {

        if ($row[6] == 'عهود') {
            $name = 'عهود طرابزوني';
            info('$row1 is: ' . $row[6]);
        } elseif ($row[6] == 'نوف') {
            $name = 'نوف الجبرني';
            info('$row2 is: ' . $row[6]);
        } elseif ($row[6] == 'سفيان') {
            $name = 'سفيان زيد';
            info('$row3 is: ' . $row[6]);
        } elseif ($row[6] == 'الموظف') {
            $name = 'ayaEng';
        } elseif ($row[6] == 'قمر'){
            info('$row4 is: ' . $row[6]);
            $name = 'قمر';
        } else {
            $name = 'ayaEng';
        }

        info('name is: ' . $name);
        // $id_user = users::where('nameUser', $name)->first()->id_user;
        // Copy
        $id_user = users::where('nameUser', 'LIKE', '%' . $name . '%')->first()->id_user;


        if ($row[9] == 'البريد الإلكتروني') {
            $email = null;
        } else {
            $email = $row[9];
        }


        return new clients([
            'date_create' =>
            $row[0] == 'تاريخ التسجيل' || $row[0] === null ? null :
                Carbon::createFromDate(1899, 12, 30)->addDays($row[0])
                ->startOfDay()
                ->format('Y-m-d H:i:s'),
            'mobile' => $row[1],
            'name_client' => $row[2],
            'sourcclient' => $row[3],
            'type_record' => $row[4],
            'type_classification' => $row[5],
            'fk_user' => $id_user,
            'reason_class' => $row[8],
            'received_date' =>
            $row[7] == 'تاريخ الإستلام' || $row[7] === null ? null :
                Carbon::createFromDate(1899, 12, 30)->addDays($row[7])
                ->startOfDay()
                ->format('Y-m-d H:i:s'),
            'SerialNumber' => 1,
            'email' => $email,
        ]);
    }

    private function dateToSerialNumber($dateString)
    {
        // Parse the date string
        $timestamp = strtotime($dateString);
        if ($timestamp === false) {
            return null; // Return null if date string is invalid
        }

        // Calculate the serial number based on Excel's date format
        $excelDate = ($timestamp / (24 * 60 * 60)) + 25569; // 25569 is the offset to convert Unix epoch to Excel epoch

        return $excelDate;
    }

    private function parseDateHandle($date)
    {
        // echo $date;
        $pattern1 = '/^\d{1,2}\/\d{1,2}\/\d{4}\s\d{1,2}:\d{2}:\d{2}\s(?:AM|PM)$/';
        $pattern2 = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/';


        $date = "8:48:00 2024/02/11 AM";
        // echo strtotime($date);
        // return ;
        $format
            = "h:i:s Y/m/d A";
        $dateTime = Carbon::createFromFormat($format, $date);

        if ($dateTime) {
            info('Date is: ' . $dateTime->toDateTimeString());
            // return 1;
        } else {
            // If the provided date doesn't match the specified format
            info('Invalid date format.');
            // return 0;
        }

        return $dateTime;
    }

    private function parseDateHandle1($dateString)
    {
        // Try parsing the date string using ISO 8601 format
        // $isoDate = Carbon::parse($dateString, 'UTC', true);
        // if ($isoDate instanceof Carbon) {
        //     return $isoDate->toDateTimeString();
        // }

        // // Try parsing the date string using custom format
        // $customDate = Carbon::createFromFormat('m/d/Y h:i:s A', $dateString);
        // if ($customDate instanceof Carbon) {
        //     return $customDate->toDateTimeString();
        // }

        // // If parsing fails, return null or handle the error as needed
        // return null;

        if (strpos($dateString, '/') !== false) {
            // Parse using Carbon for ISO 8601 format
            // $Date = Carbon::parse($dateString)->toDateTimeString();
            $Date = Carbon::createFromFormat('m/d/Y h:i:s A', $dateString);
            info('1 is: ' . $Date);
        } else {
            // Parse using strtotime for the other format
            $Date = date("Y-m-d H:i:s", strtotime($dateString));
            info('2 is: ' . $Date);
        }

        // Check if the second date string matches the format
        // if (strpos($dateString, '/') !== false) {
        //     // Parse using strtotime for the format with slashes
        //     $Date = date("Y-m-d H:i:s", strtotime($dateString));
        //     info('3 is: ' . $Date);
        // } else {
        //     // Parse using Carbon for ISO 8601 format
        //     $Date = Carbon::parse($dateString)->toDateTimeString();
        //     info('4 is: ' . $Date);
        // }
        return $Date ? $Date : null;
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // return Carbon::parse($date)->toDateTimeString();
            return Carbon::createFromFormat('n/j/Y A h:i:s', $date);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function FormatDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            return  Carbon::createFromFormat('m/d/Y h:i:s A', $date)
                ->toDateTimeString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
