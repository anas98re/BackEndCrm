<?php

namespace App\Services;

use App\Models\categorie_tiket;
use App\Models\category_ticket_fk;
use App\Models\clients;
use App\Models\subcategorie_ticket;
use App\Models\subcategory_ticket_fk;
use App\Models\ticket_detail;
use App\Models\tickets;
use App\Models\users;
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

    public function editTicketTypeService($request, $id_ticket_detail)
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
}
