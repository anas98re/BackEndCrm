<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\tickets;
use App\Http\Requests\StoreticketsRequest;
use App\Http\Requests\UpdateticketsRequest;
use App\Imports\categories_ticketImport;
use App\Imports\subcategories_ticketImport;
use App\Models\categorie_tiket;
use App\Models\subcategorie_ticket;
use App\Models\ticket_detail;
use App\Scripts\TransferTicketTable;
use App\Services\TicketDetailSrevices;
use Exception;
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

    public function addTicket(Request $request)
    {
        $respons = $this->MyService->addTicketService($request);
        return $this->sendSucssas($respons);
    }

    public function editTicketType(Request $request, $id_ticket_detail)
    {
        $ticket_detail = tickets::find($id_ticket_detail);
        if (!$ticket_detail) {
            return $this->sendError('wrong', 'This id not found');
        }
        $respons = $this->MyService->editTicketTypeService($request, $id_ticket_detail);
        return $this->TicketResponse($respons);
    }


    public function getTicketById($id)
    {
        $ticket = tickets::find($id);
        if (!$ticket) {
            return $this->sendError('wrong', 'This id not found');
        }
        $respons = $this->MyService->getTicketByIdService($ticket);
        return $this->TicketResponseToGet($respons);
    }

    public function getTicketByIdClinet($id)
    {
        $ticket = tickets::where('fk_client', $id)->first();
        if (!$ticket) {
            return $this->sendError('wrong', 'This id not found');
        }
        $respons = $this->MyService->getTicketByIdClinetService($ticket);
        return $this->TicketResponseToGet($respons);
    }

    public function getTickets()
    {
        return $this->MyService->getTicketsService();
    }

    public function TransferTicket($id, Request $request)
    {
        $respons = $this->MyService->transferTicketService($id, $request);
        return $this->sendSucssas($respons);
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

    public function reopenReport(Request $request)
    {
        $ticketDetails = ticket_detail::query()->where('fk_state', Constants::TICKET_REOPEN)->get();
        $reopenDates = ticket_detail::query()
            ->where('fk_state', Constants::TICKET_REOPEN)
            ->get()
            ->pluck('date_state');

        $response = [
            'number_of_reopen' => $ticketDetails->groupBy('tag')->count(),
            'reopen_dates' => $reopenDates,
        ];
        return $this->sendSucssas($response);
    }

    public function transferTable()
    {
        DB::beginTransaction();
        try
        {
            TransferTicketTable::transfer();

            DB::commit();
            return response()->json([
                'message' => 'success'
            ], 200);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json($e->getMessage(), 200);
        }
    }

}

// "status": [
//         {
//             "id_ticket_detail": 18,
//             "fk_ticket": 1255,
//             "fk_state": 1,
//             "tag": "KAZry",
//             "notes": "..",
//             "fk_user": 334,
//             "userName": "tt",
//             "date_state": "2024-03-24 20:30:13",
//             "stateName": "open"
//         },
//         {
//             "id_ticket_detail": 19,
//             "fk_ticket": 1255,
//             "fk_state": 2,
//             "tag": "KAZry",
//             "notes": "..",
//             "fk_user": 334,
//             "userName": "tt",
//             "date_state": "2024-03-24 20:32:23",
//             "stateName": "recive"
//         },
//         {
//             "id_ticket_detail": 20,
//             "fk_ticket": 1255,
//             "fk_state": 3,
//             "tag": "KAZry",
//             "notes": "..",
//             "fk_user": 334,
//             "userName": "tt",
//             "date_state": "2024-03-24 20:32:41",
//             "stateName": "close"
//         }
//         {
//             "id_ticket_detail": 23,
//             "fk_ticket": 1255,
//             "fk_state": 6,
//             "tag": "aKfAp",
//             "notes": "..",
//             "fk_user": 334,
//             "userName": "tt",
//             "date_state": "2024-03-24 20:36:03",
//             "stateName": "reopen"
//         },
//         {
//             "id_ticket_detail": 24,
//             "fk_ticket": 1255,
//             "fk_state": 2,
//             "tag": "aKfAp",
//             "notes": "..",
//             "fk_user": 334,
//             "userName": "tt",
//             "date_state": "2024-03-24 20:36:35",
//             "stateName": "recive"
//         }
// ]
