<?php

namespace App\Imports;

use App\Models\company;
use App\Models\company_comment;
use App\Models\users;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;

class CompanyImport implements ToModel
{
    public function model(array $row)
    {
        $CompanyInfo = [
            'VMW2ha6uR5mSyJHQphijqQ' => 'SMART LIFE',
            'FsK1hNapTS6URXhGXgbURA' => 'فودكس',
            '0eGHTya6TC2HnTl-maZiWQ' => 'ميكروتيك',
            'T3uEvoIxTPqUzS1YZne2jQ' => 'رواء',
            'TqORSzkFQ5KXnP12t0acNg' => 'دفاتر',
            '4448fK1GTJqqCwVo2Hpxsg' => 'سهل',
            'qaXIecnQQJaQpV303fdETA' => 'الأمين',
            'bibvmh62TJa4D8TJsbeaqw' => 'قيود',
            'Ir9VRWIsQSaV6I3k-xLFsQ' => 'SMACC - سماك',
            'qPo1S183SVOdNHuIPsdeIw' => 'دفترة',
            'crOn1Nf7Q2yRKHo9nrfPjg' => 'الحلول النهائية يمن سوفت ( اونكس )'
        ];

        $companyName = null;

        foreach ($CompanyInfo as $key => $value) {
            if ($key == $row[0]) {
                $companyName = $value;
                break;
            }
        }


        if ($row[1] != 'Username') {
            $user = users::where('nameUser', $row[1])->first();
            if (!$user) {
                $userId = 1;
            } else {
                $userId = $user->id_user;
            }
        } else {
            $userId = 1;
        }

        if ($companyName === null) {
            $companyId = 1;
        } else {
            $company = company::where('name_company', $companyName)->first();
            if ($company !== null) {
                $companyId = $company->id_Company;
            } else {
                $companyId = 1;
            }
        }


        $Date = $this->parseDate($row[3]);

        return new company_comment([
            'fk_user' => $userId,
            'fk_company' => $companyId,
            'date_comment' => $Date,
            'content' => $row[4],
        ]);
    }


    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->toDateTimeString();
        } catch (\Exception $e) {
            return null; // Return null if unable to parse the date
        }
    }
}
