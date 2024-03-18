<?php

namespace App\Imports;

use App\Models\importantLink;
use App\Models\subcategorie_ticket;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class subcategories_ticketImport implements ToModel
{

    public function model(array $row)
    {
        return new subcategorie_ticket([
            'sub_category_ar' => $row[9],
            'sub_category_en' => $row[10],
            'classification' => $row[11],
        ]);
    }

}
