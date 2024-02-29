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

class AnotherDateClientsImport implements ToModel
{

    public function model(array $row)
    {
        $counts = [];
        $counts[0] = 0;
        $clientMobile = clients::where('mobile', $row[1])->first();
        if (!$clientMobile) {
            $clientName = clients::where('name_client', $row[2])->first();
            if (!$clientName) {
                info('line ');
                if ($row[6] == 'عهود') {
                    $id_user = 208;
                    $name = 'عهود طرابزوني';
                } elseif ($row[6] == 'نوف') {
                    $name = 'نوف الجبرني';
                    $id_user = users::where('nameUser', 'LIKE', '%' . $name . '%')->first()->id_user;
                } elseif ($row[6] == 'سفيان') {
                    $id_user = 74;
                    $name = 'سفيان زيد';
                } elseif ($row[6] == 'الموظف') {
                    $id_user = 1;
                    $name = 'ayaEng';
                } elseif ($row[6] == 'قمر') {
                    $id_user = 120;
                    $name = 'قمر';
                } else {
                    $id_user = 1;
                    $name = 'ayaEng';
                }


                if ($row[5] == 'إهتمام مختلف') {
                    $row[5] = 'اهتمام مختلف';
                }


                // return new clients([
                //     'date_create' =>
                //     $row[0] == 'تاريخ التسجيل' || $row[0] === null ? null :
                //         Carbon::createFromDate(1899, 12, 30)->addDays($row[0])
                //         ->startOfDay()
                //         ->format('Y-m-d H:i:s'),
                //     'mobile' => $row[1],
                //     'name_client' => $row[2],
                //     'sourcclient' => $row[3],
                //     'type_record' => $row[4],
                //     'type_classification' => $row[5],
                //     'fk_user' => $id_user,
                //     'SerialNumber' => 1
                // ]);
            }
        }
    }
}
