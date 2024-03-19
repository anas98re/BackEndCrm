<?php

namespace App\Http\Controllers;

use App\Models\tickets;
use App\Http\Requests\StoreticketsRequest;
use App\Http\Requests\UpdateticketsRequest;
use App\Imports\categories_ticketImport;
use App\Imports\subcategories_ticketImport;
use App\Models\categorie_tiket;
use App\Models\subcategorie_ticket;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class TicketsController extends Controller
{
    public function importCategoriesTicket(Request $request)
    {
        $file = $request->file('file');

        Excel::import(new categories_ticketImport, $file);

        return $this->sendResponse('success', 'categories_ticket imported successfully.');
    }

    public function importSubCategoriesTicket(Request $request)
    {
        $file = $request->file('file');

        Excel::import(new subcategories_ticketImport, $file);

        return $this->sendResponse('success', 'sub_categories_ticket imported successfully.');
    }

    public function getCategoriesTicket()
    {
        $CategoriesTicket = categorie_tiket::all();
        return $this->sendSucssas($CategoriesTicket);
    }

    public function getSubCategoriesTicket()
    {
        $CategoriesTicket = subcategorie_ticket::all();
        return $this->sendSucssas($CategoriesTicket);
    }
}
