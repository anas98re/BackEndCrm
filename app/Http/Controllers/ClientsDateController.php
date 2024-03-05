<?php

namespace App\Http\Controllers;

use App\Models\clients_date;
use App\Http\Requests\Storeclients_dateRequest;
use App\Http\Requests\Updateclients_dateRequest;
use App\Models\agent;
use App\Models\client_comment;
use App\Models\clients;
use App\Models\notifiaction;
use App\Notifications\SendNotification;
use App\Services\ClientsDateService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ClientsDateController extends Controller
{
    private $myService;

    public function __construct(ClientsDateService $myService)
    {
        $this->myService = $myService;
    }
    public function rescheduleOrCancelVisitClient(Request $request, $idclients_date)
    {
        DB::beginTransaction();

        try {
            if ($request->typeProcess == 'reschedule') {
                $client = clients_date::where('idclients_date', $idclients_date)
                    ->update([
                        'is_done' => 3,
                        'date_client_visit' => $request->date_client_visit,
                        'processReason' => $request->processReason,
                        'type_date' => $request->type_date,
                        'fk_user_update' => auth('sanctum')->user()->id_user,
                        'date_end' => $request->date_end,
                        'fk_user' => $request->fk_user
                    ]);

                    $this->myService->handleNotificationAndComments(
                    $privilage_id = 59,
                    $typeProcess = 'اعادة جدولة زيارة',
                    $idclients_date,
                    $processReason = $request->processReason
                );
            } elseif ($request->typeProcess == 'cancel') {
                $client = clients_date::where('idclients_date', $idclients_date)
                    ->update([
                        'is_done' => 2,
                        'processReason' => $request->processReason,
                        'fk_user_update' => auth('sanctum')->user()->id_user
                    ]);

                    $this->myService->handleNotificationAndComments(
                    $privilage_id = 181,
                    $typeProcess = 'الغاء زيارة',
                    $idclients_date,
                    $processReason = null
                );
            } else {
                return;
            }

            DB::commit();
            // return $this->sendResponse(['message' => 'done'], 200);
            return response()->json(['success' => true, 'message' => 'done', 'code' => 200]);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendResponse(['message' => 'Failed to process. Please try again.'], 500);
        }
    }



    public function getDateVisitAgentFromQuery($agentId)
    {
        $result = DB::table('clients_date AS dd')
            ->join('agent as AG', 'AG.id_agent', '=', 'dd.fk_agent')
            ->select('dd.*', 'AG.name_agent')
            ->where('dd.fk_agent', $agentId)
            ->get();

        return $this->sendResponse($result, 'Done');
    }
}
