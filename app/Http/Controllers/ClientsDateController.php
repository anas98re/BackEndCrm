<?php

namespace App\Http\Controllers;

use App\Models\clients_date;
use App\Http\Requests\Storeclients_dateRequest;
use App\Http\Requests\Updateclients_dateRequest;
use App\Models\agent;
use App\Models\agentComment;
use App\Models\client_comment;
use App\Models\clients;
use App\Models\notifiaction;
use App\Notifications\SendNotification;
use App\Services\ClientsDateService;
use Carbon\Carbon;
use Exception;
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

                $client = clients_date::find($idclients_date);

                if ($client) {
                    $client->is_done = 3;
                    $client->date_client_visit = $request->date_client_visit;
                    $client->processReason = $request->processReason;
                    $client->type_date = $request->type_date;
                    $client->fk_user_update = auth('sanctum')->user()->id_user;
                    $client->date_end = $request->date_end;
                    $client->fk_user = $request->fk_user;
                    $client->date_update_visit = Carbon::now('Asia/Riyadh');

                    $client->save();
                }

                $this->myService->handleNotificationAndComments(
                    $privilage_id = 59,
                    $typeProcess = 'اعادة جدولة زيارة',
                    $idclients_date,
                    $processReason = $request->processReason
                );
            } elseif ($request->typeProcess == 'cancel') {

                $client = clients_date::find($idclients_date);

                if ($client) {
                    $client->is_done = 2;
                    $client->processReason = $request->processReason;
                    $client->fk_user_update = auth('sanctum')->user()->id_user;
                    $client->date_update_visit = Carbon::now('Asia/Riyadh');

                    $client->save();
                }

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

    public function updateStatusForVisit(Request $request, $date_id)
    {
        DB::beginTransaction();
        $data = $request->all();
        try
        {
            $client_date = clients_date::query()->where('idclients_date', $date_id)->first();
            if( is_null ($client_date) )
            {
                return response()->json(['message' => 'client date not found'], 404);
            }
            if(! is_null($data['fk_client']?? null))
            {
                $this->addComment($data['comment'], $data['fk_client'], auth()->user()->id_user, 'زيارة عميل');
            }
            else
            {
                $this->addCommentAgent($data['comment'], $data['fk_agent'], auth()->user()->id_user, 'زيارة وكيل');
            }

            $client_date->update([
                'is_done' => $data['is_done'],
                'fk_user_done' => auth()->user()->id_user,
                'date_done' => Carbon::now()->format("Y-m-d H:i:s"),
            ]);

            $resJson = array("result" => "success", "code" => "200", "message" => 'done');
            DB::commit();
            return response()->json($resJson);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    protected function addComment($content,$fk_client,$fk_user,$type_comment)
    {
        $data['fk_client'] = $fk_client;
        $data['fk_user'] = $fk_user;
        $data['date_comment'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['content'] = $content;
        $data['type_comment'] = $type_comment;

        return client_comment::create($data);
    }

    protected function addCommentAgent($content,$agent_id,$fk_user,$type_comment)
    {
        $data['agent_id'] = $agent_id;
        $data['user_id'] = $fk_user;
        $data['date_comment'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['content'] = $content;
        $data['type_comment'] = $type_comment;

        return agentComment::create($data);
    }
}
