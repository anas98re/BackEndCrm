<?php

namespace App\Imports;

use App\Models\importantLink;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportantLinksImport implements ToModel
{

    public function model(array $row)
    {
        $addDate = $this->parseDate($row[8]);
        $editDate = $this->parseDate($row[9]);
        return new importantLink([
            'title' => $row[6],
            'address' => $row[5],
            'link' => $row[2],
            'clause' => $row[4],
            'notes' => $row[1],
            'add_date' => $addDate,
            'edit_date' => $editDate,
            'user_id' => 1,
            'department' => $row[0],

        ]);
    }

    private function parseDate($date)
    {
        // Check if the date is empty
        if (empty($date)) {
            return null; // Return null if date is empty
        }

        try {
            // Attempt to parse the date using Carbon
            return Carbon::parse($date)->toDateTimeString();
        } catch (\Exception $e) {
            // Handle parsing errors
            return null; // Return null if unable to parse the date
        }
    }
}
