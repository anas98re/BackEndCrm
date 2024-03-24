<?php

namespace App\Services;

use App\Constants;
use App\Models\categorie_tiket;
use App\Models\category_ticket_fk;
use App\Models\clients;
use App\Models\subcategorie_ticket;
use App\Models\subcategory_ticket_fk;
use App\Models\ticket_detail;
use App\Models\ticket_state;
use App\Models\tickets;
use App\Models\users;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use PHPUnit\TextUI\Configuration\Constant;
use Illuminate\Support\Facades\Cache;

class TicketDetailSrevices extends JsonResponeService
{

    public function addOrReOpenTicketService($request)
    {
        try {
            DB::beginTransaction();

            $requestHandle = $request->all();
            $openType = $request->input('open_type');

            $ticket = tickets::create($requestHandle);
            $ticketState = ($openType == 'open') ? Constants::TICKET_OPEN : Constants::TICKET_REOPEN;

            ticket_detail::create([
                'fk_ticket' => $ticket->id_ticket,
                'fk_state' => $ticketState,
                'fk_user' => auth('sanctum')->user()->id_user,
                'date_state' => Carbon::now('Asia/Riyadh')->toDateTimeString(),
                'notes' => $request->notes
            ]);

            DB::commit();

            return $ticket;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function editTicketTypeService($request, $id)
    {
        $createdCategories = null;
        $createdSubcategories = null;
        $ticket = tickets::find($id);

        $Client = clients::find($ticket->fk_client);
        $name_enterprise = $Client ? $Client->name_enterprise : null;
        $currentUserId = auth('sanctum')->user()->id_user;
        $nameUser = users::find($currentUserId)->nameUser;
        $nowDate = Carbon::now('Asia/Riyadh')->toDateTimeString();

        switch ($request->type_ticket) {
            case 'recive':
                $ticket->update($request->all());
                $this->updateTicketState($id, Constants::TICKET_RECIVE, $currentUserId, $nowDate, $request->notes);
                break;
            case 'close':
                try {
                    DB::beginTransaction();
                    $ticket->update($request->all());
                    $this->updateTicketState($id, Constants::TICKET_CLOSE, $currentUserId, $nowDate, $request->notes);
                    $createdCategories = $this->createCategories($request, $id);
                    $createdSubcategories = $this->createSubcategories($request, $id);

                    DB::commit();
                    break;
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
            case 'rate':
                $ticket->update($request->all());
                $this->updateTicketState($id, Constants::TICKET_RATE, $currentUserId, $nowDate, $request->notes);
                break;
            default:
                break;
        }

        return [
            'ticket' => $ticket,
            'Categories' => $createdCategories,
            'Subcategories' => $createdSubcategories,
            'nameUser' =>  $nameUser,
            'name_enterprise' => $name_enterprise
        ];
    }

    private function updateTicketState($ticketId, $state, $userId, $date, $notes)
    {
        $ticketState = ticket_detail::where('fk_ticket', $ticketId)->first();
        $ticketState->fk_state = $state;
        $ticketState->fk_user = $userId;
        $ticketState->date_state = $date;
        $ticketState->notes = $notes;
        $ticketState->save();
    }



    private function createCategories($request, $id_ticket)
    {
        $categoriesTicketFks = json_decode($request->input('categories_ticket_fk'));
        $createdCategories = [];
        if (is_array($categoriesTicketFks)) {
            foreach ($categoriesTicketFks as $fk) {
                $categoryTicketFk = category_ticket_fk::create([
                    'fk_category' => $fk,
                    'fk_ticket' => $id_ticket
                ]);

                // Retrieve category name from categories_ticket table
                $categoryTicketResponse = null;
                $category = categorie_tiket::where('id', $fk)->first();
                if ($category) {
                    $categoryTicketResponse = [
                        'category_ar' => $category->category_ar,
                        'category_en' => $category->category_en,
                        'id' => $category->id,
                        'classification' => $category->classification,
                        'row_id' => $categoryTicketFk->id
                    ];
                }

                $createdCategories[] = $categoryTicketResponse;
            }
        }
        return $createdCategories;
    }

    private function createSubcategories($request, $id_ticket)
    {
        $subcategoriesTicketFks = json_decode($request->input('subcategories_ticket'));
        $createdSubcategories = [];
        if (is_array($subcategoriesTicketFks)) {
            foreach ($subcategoriesTicketFks as $fk) {
                $subcategoryTicketFk = subcategory_ticket_fk::create([
                    'fk_subcategory' => $fk,
                    'fk_ticket' => $id_ticket
                ]);

                // Retrieve subcategory name from subcategories_ticket table
                $subcategoryTicketResponse = null;
                $subcategory = subcategorie_ticket::where('id', $fk)->first();
                if ($subcategory) {
                    $subcategoryTicketFk->sub_category_ar = $subcategory->sub_category_ar;
                    $subcategoryTicketFk->sub_category_en = $subcategory->sub_category_en;
                    $subcategoryTicketResponse = [
                        'sub_category_ar' => $subcategory->sub_category_ar,
                        'sub_category_en' => $subcategory->sub_category_en,
                        'id' => $subcategory->id,
                        'classification' => $subcategory->classification,
                        'row_id' => $subcategoryTicketFk->id
                    ];
                }

                $createdSubcategories[] = $subcategoryTicketResponse;
            }
        }

        return $createdSubcategories;
    }

    public function getTicketByIdService($ticket)
    {
        $client = clients::find($ticket->fk_client);
        $name_enterprise = $client ? $client->name_enterprise : null;

        $ticket_detail = ticket_detail::where('fk_ticket', $ticket->id_ticket)->first();
        $UserId = $ticket_detail ? $ticket_detail->fk_user : null;
        $User = users::find($UserId);
        $nameUser = $User ? $User->nameUser : null;

        $Subcategories = $this->getSubcategories($ticket->id_ticket);
        $categories = $this->getCategories($ticket->id_ticket);

        $response = [
            'ticket' => $ticket,
            'name_enterprise' => $name_enterprise,
            'nameUser' => $nameUser,
            'Categories' => $categories,
            'Subcategories' => $Subcategories
        ];

        return $response;
    }

    public function getTicketsService()
    {
        $tickets = tickets::select(
            'tickets.*',
            'cl.name_client',
            'cl.name_enterprise',
            'r.name_regoin',
            'r.fk_country',
            'useropen.nameUser as nameuseropen',
            'userrecive.nameUser as nameuserrecive',
            'userclose.nameUser as nameuserclose',
            'userrate.nameUser as nameuserrate',
            'cl.mobile'
        )
            ->join('clients as cl', 'cl.id_clients', '=', 'tickets.fk_client')
            ->join('regoin as r', 'r.id_regoin', '=', 'cl.fk_regoin')
            ->join('users as useropen', 'useropen.id_user', '=', 'tickets.fk_user_open')
            ->leftJoin('users as userrecive', 'userrecive.id_user', '=', 'tickets.fk_user_recive')
            ->leftJoin('users as userclose', 'userclose.id_user', '=', 'tickets.fk_user_close')
            ->leftJoin('users as userrate', 'userrate.id_user', '=', 'tickets.fkuser_rate')
            ->orderByDesc('tickets.id_ticket')
            ->get();

        $response = [];

        foreach ($tickets as $ticket) {
            $responseTicket1 = category_ticket_fk::select(
                'categories_ticket_fks.id',
                'categories_ticket.category_ar',
                'categories_ticket.category_en',
                'categories_ticket.classification'
            )
                ->leftJoin('tickets', 'tickets.id_ticket', '=', 'categories_ticket_fks.fk_ticket')
                ->leftJoin('categories_ticket', 'categories_ticket.id', '=', 'categories_ticket_fks.fk_category')
                ->where('tickets.id_ticket', $ticket->id_ticket)
                ->get();

            $responseTicket2 = subcategory_ticket_fk::select(
                'subcategories_ticket_fks.id',
                'subcategories_ticket.sub_category_ar',
                'subcategories_ticket.sub_category_en',
                'subcategories_ticket.classification'
            )
                ->leftJoin('tickets', 'tickets.id_ticket', '=', 'subcategories_ticket_fks.fk_ticket')
                ->leftJoin('subcategories_ticket', 'subcategories_ticket.id', '=', 'subcategories_ticket_fks.fk_subcategory')
                ->where('tickets.id_ticket', $ticket->id_ticket)
                ->get();

            $ticketData = $ticket->toArray();
            $ticketData['categories_ticket_fk'] = $responseTicket1->toArray();
            $ticketData['subcategories_ticket_fk'] = $responseTicket2->toArray();

            $response[] = $ticketData;
        }

        $response = [
            'result' => 'success',
            'code' => 200,
            'message' => $response
        ];


        return response()->json($response, 200);
    }

    private function getSubcategories($ticketId)
    {
        $Subcategories = [];
        $subcategory_ticket_fks = subcategory_ticket_fk::where('fk_ticket', $ticketId)->get();

        foreach ($subcategory_ticket_fks as $fk) {
            $subcategory = subcategorie_ticket::find($fk->fk_subcategory);

            if ($subcategory) {
                $subcategoryTicketResponse = [
                    'sub_category_ar' => $subcategory->sub_category_ar,
                    'sub_category_en' => $subcategory->sub_category_en,
                    'id' => $subcategory->id,
                    'classification' => $subcategory->classification
                ];
                $Subcategories[] = $subcategoryTicketResponse;
            }
        }

        return $Subcategories;
    }

    private function getCategories($ticketId)
    {
        $categories = [];
        $category_ticket_fks = category_ticket_fk::where('fk_ticket', $ticketId)->get();

        foreach ($category_ticket_fks as $fk) {
            $category = categorie_tiket::find($fk->fk_category);

            if ($category) {
                $categoryTicketResponse = [
                    'category_ar' => $category->category_ar,
                    'category_en' => $category->category_en,
                    'id' => $category->id,
                    'classification' => $category->classification
                ];
                $categories[] = $categoryTicketResponse;
            }
        }

        return $categories;
    }







    // To privous way
    public function addOrReOpenTicketService1($request)
    {
        try {
            DB::beginTransaction();
            $requestHandle = $request->all();
            if ($request->input('open_type') == 0) {
                $requestHandle['type_ticket_reopen'] = 0;
                $requestHandle['type_ticket'] = 'جديدة';
                $requestHandle['fk_user_open'] = auth('sanctum')->user()->id_user;
                $requestHandle['date_open'] = Carbon::now('Asia/Riyadh')->toDateTimeString();
            }
            if ($request->input('open_type') == 1) {
                $requestHandle['type_ticket_reopen'] = 1;
                $requestHandle['type_ticket'] = 'اعادة فتح';
                $requestHandle['fk_user_reopen'] = auth('sanctum')->user()->id_user;
                $requestHandle['date_reopen'] = Carbon::now('Asia/Riyadh')->toDateTimeString();
            }
            $data = ticket_detail::create($requestHandle);
            DB::commit();
            return $data;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }


    public function editTicketTypeService1($request, $id_ticket_detail)
    {
        $createdCategories = null;
        $createdSubcategories = null;
        $ticket_detail = ticket_detail::find($id_ticket_detail);
        $Client = clients::find($ticket_detail->fk_client);
        $name_enterprise = $Client ? $Client->name_enterprise : null;
        $currentUserId = auth('sanctum')->user()->id_user;
        $nameUser = users::find($currentUserId)->nameUser;
        $nowDate = Carbon::now('Asia/Riyadh')->toDateTimeString();
        switch ($request->type_ticket) {
            case 'قيد التنفيذ':
                $ticket_detail->type_ticket = 'قيد التنفيذ';
                $ticket_detail->fk_user_recive = $currentUserId;
                $ticket_detail->date_recive = $nowDate;
                break;
            case 'مغلقة':
                try {
                    DB::beginTransaction();
                    $ticket_detail->type_ticket = 'مغلقة';
                    $ticket_detail->fk_user_close = $currentUserId;
                    $ticket_detail->date_close = $nowDate;
                    $ticket_detail->notes_ticket = $request->notes_ticket;;
                    $ticket_detail->save();

                    $createdCategories = $this->createCategories($request, $id_ticket_detail);
                    $createdSubcategories = $this->createSubcategories($request, $id_ticket_detail);

                    DB::commit();
                    return [
                        'ticket' => $ticket_detail,
                        'Categories' => $createdCategories,
                        'Subcategories' => $createdSubcategories,
                        'nameUser' =>  $nameUser,
                        'name_enterprise' => $name_enterprise
                    ];
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
                break;
            case 'تم التقييم':
                $ticket_detail->type_ticket = 'تم التقييم';
                $ticket_detail->fkuser_rate = $currentUserId;
                $ticket_detail->date_rate = $nowDate;
                $ticket_detail->notes_rate = $request->notes_rate;
                $ticket_detail->rate = $request->rate;
                break;

            default:
                break;
        }
        $ticket_detail->save();
        return [
            'ticket' => $ticket_detail,
            'Categories' => $createdCategories,
            'Subcategories' => $createdSubcategories,
            'nameUser' =>  $nameUser,
            'name_enterprise' => $name_enterprise
        ];
    }

    public function getTicketByIdService1($ticket_detail)
    {
        $client = clients::find($ticket_detail->fk_client);
        $name_enterprise = $client ? $client->name_enterprise : null;

        $User = users::find(
            $ticket_detail->fk_user_open ?
                $ticket_detail->fk_user_open :
                $ticket_detail->fk_user_reopen
        );
        $nameUser = $User->nameUser;

        $Subcategories = null;
        $subcategory_ticket_fks = subcategory_ticket_fk::where(
            'fk_ticket',
            $ticket_detail->id_ticket_detail
        )->get();
        info($subcategory_ticket_fks);
        foreach ($subcategory_ticket_fks as $fk) {
            $subcategory = subcategorie_ticket::where('id', $fk->fk_subcategory)->first();
            $subcategoryTicketResponse = [
                'sub_category_ar' => $subcategory->sub_category_ar,
                'sub_category_en' => $subcategory->sub_category_en,
                'id' => $subcategory->id,
                'classification' => $subcategory->classification
            ];
            $Subcategories[] = $subcategoryTicketResponse;
        }

        $categories = null;
        $category_ticket_fks = category_ticket_fk::where(
            'fk_ticket',
            $ticket_detail->id_ticket_detail
        )->get();
        foreach ($category_ticket_fks as $fk) {
            $category = categorie_tiket::where('id', $fk->fk_category)->first();
            $categoryTicketResponse = [
                'category_ar' => $category->category_ar,
                'category_en' => $category->category_en,
                'id' => $category->id,
                'classification' => $category->classification
            ];
            $categories[] = $categoryTicketResponse;
        }

        $response = [
            'ticket' => $ticket_detail,
            'name_enterprise' => $name_enterprise,
            'nameUser' => $nameUser,
            'Categories' => $categories,
            'Subcategories' => $Subcategories
        ];
        return $response;
    }
}
