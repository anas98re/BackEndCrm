<?php

namespace App\Services;

use App\Models\categorie_tiket;
use App\Models\category_ticket_fk;
use App\Models\subcategorie_ticket;
use App\Models\subcategory_ticket_fk;
use App\Models\ticket_detail;
use App\Models\tickets;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TicketDetailSrevices extends JsonResponeService
{
    public function addOrReOpenTicketService($request)
    {
        try {
            DB::beginTransaction();
            $requestHandle = $request->all();
            if ($request->has('type_ticket')) {
                $requestHandle['type_ticket'] = 0;
                $requestHandle['fk_user_open'] = auth('sanctum')->user()->id_user;
                $requestHandle['date_open'] = Carbon::now('Asia/Riyadh')->toDateTimeString();
            }
            if ($request->has('type_ticket_reopen')) {
                $requestHandle['type_ticket_reopen'] = 1;
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

    public function editTicketTypeService($request, $id_ticket_detail)
    {
        try {
            DB::beginTransaction();
            $ticket_detail = ticket_detail::find($id_ticket_detail);
            $currentUserId = auth('sanctum')->user()->id_user;
            $nowDate = Carbon::now('Asia/Riyadh')->toDateTimeString();
            switch ($request->type_ticket) {
                case 'استلام التذكرة':
                    $ticket_detail->type_ticket = 'قيد التنفيذ';
                    $ticket_detail->fk_user_recive = $currentUserId;
                    $ticket_detail->date_recive = $nowDate;
                    break;
                case 'اغلاق التذكرة':
                    $ticket_detail->type_ticket = 'مغلقة';
                    $ticket_detail->fk_user_close = $currentUserId;
                    $ticket_detail->date_close = $nowDate;
                    break;
                case 'تقييم بعد الاغلاق':
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
            DB::commit();
            return $ticket_detail;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function closeTicketService($request, $id_ticket)
    {
        try {
            DB::beginTransaction();
            $ticket = tickets::find($id_ticket);
            if ($ticket) {
                $ticket->update([
                    'fk_user_close' => auth('sanctum')->user()->id_user,
                    'date_close' => Carbon::now('Asia/Riyadh')->toDateTimeString(),
                    'type_ticket' => $request->input('type_ticket'),
                    'notes_ticket' => $request->input('notes_ticket')
                ]);
            }

            $createdCategories = $this->createCategories($request, $id_ticket);
            $createdSubcategories = $this->createSubcategories($request, $id_ticket);

            DB::commit();

            return [
                'ticket' => $ticket,
                'Categories' => $createdCategories,
                'Subcategories' => $createdSubcategories
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
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
                $category = categorie_tiket::where('id', $fk)->first();
                if ($category) {
                    $categoryTicketResponse = [
                        'category_ar' => $category->category_ar,
                        'category_en' => $category->category_en,
                        'id' => $category->id,
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
                $subcategory = subcategorie_ticket::where('id', $fk)->first();
                if ($subcategory) {
                    $subcategoryTicketFk->sub_category_ar = $subcategory->sub_category_ar;
                    $subcategoryTicketFk->sub_category_en = $subcategory->sub_category_en;
                    $subcategoryTicketResponse = [
                        'sub_category_ar' => $subcategory->sub_category_ar,
                        'sub_category_en' => $subcategory->sub_category_en,
                        'id' => $subcategory->id,
                        'row_id' => $subcategoryTicketFk->id
                    ];
                }

                $createdSubcategories[] = $subcategoryTicketResponse;
            }
        }

        return $createdSubcategories;
    }
}
