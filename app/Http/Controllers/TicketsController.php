<?php

namespace App\Http\Controllers;

use App\Models\tickets;
use App\Http\Requests\StoreticketsRequest;
use App\Http\Requests\UpdateticketsRequest;
use App\Imports\categories_ticketImport;
use App\Imports\subcategories_ticketImport;
use App\Models\categorie_tiket;
use App\Models\subcategorie_ticket;
use App\Services\TicketDetailSrevices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class TicketsController extends Controller
{
    private $MyService;

    public function __construct(TicketDetailSrevices $MyService)
    {
        $this->MyService = $MyService;
    }

    public function addOrReOpenTicket(Request $request)
    {
        $respons = $this->MyService->addOrReOpenTicketService($request);
        return $this->sendSucssas($respons);
    }

    public function editTicketType(Request $request, $id_ticket_detail)
    {
        $respons = $this->MyService->editTicketTypeService($request, $id_ticket_detail);
        return $this->sendSucssas($respons);
    }

    public function closeTicket(Request $request, $id_ticket)
    {
        $respons = $this->MyService->closeTicketService($request, $id_ticket);
        return $this->closeTicketResponse($respons);
    }

    private function closeTicketResponse($result)
    {
        $response = [
            'result' => 'success',
            'code' => 200,
            'message' => [
                'ticket' => $result['ticket'],
                'Categories' => $result['Categories'],
                'Subcategories' => $result['Subcategories']
            ]
        ];
        return response()->json($response, 200);
    }

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
