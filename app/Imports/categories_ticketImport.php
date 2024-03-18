<?php

namespace App\Imports;

use App\Models\categorie_tiket;
use App\Models\importantLink;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class categories_ticketImport implements ToModel
{

    public function model(array $row)
    {
        return new categorie_tiket([
            'category_ar' => $row[0],
            'category_en' => $row[1],
            'classification' => $row[2],
        ]);
    }

}
