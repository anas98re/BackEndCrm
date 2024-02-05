<?php

namespace App\Exports;

use App\Models\importantLink;
use Maatwebsite\Excel\Concerns\FromCollection;

class ImportantLinksExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return importantLink::all();
    }
}
