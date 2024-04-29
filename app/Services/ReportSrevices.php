<?php

namespace App\Services;

use App\Constants;
use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\activity_type;
use App\Models\agent;
use App\Models\ChangeLog;
use App\Models\city;
use App\Models\clients;
use App\Models\company;
use App\Models\levelModel;
use App\Models\managements;
use App\Models\notifiaction;
use App\Models\participate;
use App\Models\regoin;
use App\Models\tsks_group;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ReportSrevices extends JsonResponeService
{
    public function handle(
        $modelId,
        $model,
        $dataBeforeUpdate,
        $dataAfterUpdate,
        $userId,
        $update_source,
        $routePattern,
        $description,
        $nameMainCitiesBefor,
        $isApprove
    ) {
        $dataBeforeUpdate = $dataBeforeUpdate;
        $dataAfterUpdate = $dataAfterUpdate;

        $dataBeforeUpdateArray = json_decode($dataBeforeUpdate, true);
        $dataAfterUpdateArray = json_decode($dataAfterUpdate, true);

        // Find differing values
        $differences = [];
        foreach ($dataBeforeUpdateArray as $key => $value) {
            if ($value !== $dataAfterUpdateArray[$key]) {
                $differences[$key] = $dataAfterUpdateArray[$key];
            }
        }

        if ($differences) {
            info(3);
            $report = $this->generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate, $nameMainCitiesBefor);
            info(4);
            $reportMessage = implode("\n", $report);

            ChangeLog::create([
                'model' => $model,
                'action' => 'updated',
                'changesData' => $reportMessage,
                'description' => $description,
                'user_id' => (int) $userId,
                'model_id' => $modelId,
                'edit_date' => Carbon::now('Asia/Riyadh')->toDateTimeString(),
                'source' => $update_source,
                'route' => $routePattern,
                'afterApprove' => $isApprove,
                'ip' => null
            ]);
        }
    }

    private function generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate, $nameMainCitiesBefor)
    {
        info('$differences for invoices: ', array($differences));
        $report = [];
        foreach ($differences as $key => $value) {
            info($differences[$key]);
            info('$differences[$key]');
            info($dataBeforeUpdate[$key]);
            switch ($key) {
                    // clients
                case 'city':
                    $cityBefore = city::where('id_city', $dataBeforeUpdate[$key])->first()?->name_city;
                    $cityAfter = city::where('id_city', $dataAfterUpdate[$key])->first()?->name_city;
                    $report[] = 'city: (' . $cityBefore . ') TO (' . $cityAfter . ')';
                    break;
                case 'activity_type_fk':
                    $activityBefore = activity_type::where('id_activity_type', $dataBeforeUpdate[$key])->first()?->name_activity_type;
                    $activityAfter = activity_type::where('id_activity_type', $dataAfterUpdate[$key])->first()?->name_activity_type;
                    $report[] = 'activity_type: (' . $activityBefore . ') TO (' . $activityAfter . ')';
                    break;
                case 'presystem':
                    $presystemBefore = company::where('id_Company', $dataBeforeUpdate[$key])->first()?->name_company;
                    $presystemAfter = company::where('id_Company', $dataAfterUpdate[$key])->first()?->name_company;
                    $report[] = 'presystem: (' . $presystemBefore . ') TO (' . $presystemAfter . ')';
                    break;
                    // Invoices
                case 'participate_fk':
                    $participateBefore = 'not_found';
                    $participateAfter = 'not_found';
                    if ($dataBeforeUpdate[$key]) {
                        $participateBefore = participate::where('id_participate', $dataBeforeUpdate[$key])->first()?->name_participate;
                    }
                    if ($dataAfterUpdate[$key]) {
                        $participateAfter = participate::where('id_participate', $dataAfterUpdate[$key])->first()?->name_participate;
                    }
                    $report[] = 'participate: (' . $participateBefore . ') TO (' . $participateAfter . ')';
                    break;
                case 'fk_agent':
                    $agentBefore = 'not_found';
                    $agentAfter = 'not_found';
                    if ($dataBeforeUpdate[$key]) {
                        $agentBefore = agent::where('id_agent', $dataBeforeUpdate[$key])->first()?->name_agent;
                    }
                    if ($dataAfterUpdate[$key]) {
                        $agentAfter = agent::where('id_agent', $dataAfterUpdate[$key])->first()?->name_agent;
                    }
                    $report[] = 'agent: (' . $agentBefore . ') TO (' . $agentAfter . ')';
                    break;
                case 'type_seller':
                    $typeSellerOptions = ['موزع', 'وكيل', 'متعاون', 'موظف'];
                    $typeSellerAfter = $typeSellerOptions[$dataAfterUpdate[$key]] ?? 'موظف';
                    $typeSellerBefore = $typeSellerOptions[$dataBeforeUpdate[$key]] ?? 'موظف';
                    $report[] = 'type_seller: (' . $typeSellerBefore . ') TO (' . $typeSellerAfter . ')';
                    break;
                case 'fk_idUser':
                    $userBefore = users::where('id_user', $dataBeforeUpdate[$key])->first()?->nameUser;
                    $userAfter = users::where('id_user', $dataAfterUpdate[$key])->first()?->nameUser;
                    $report[] = 'User: (' . $userBefore . ') TO (' . $userAfter . ')';
                    break;
                case 'fk_idClient':
                    $userBefore = clients::where('id_clients', $dataBeforeUpdate[$key])->first()?->name_enterprise;
                    $userAfter = clients::where('id_clients', $dataAfterUpdate[$key])->first()?->name_enterprise;
                    $report[] = 'client: (' . $userBefore . ') TO (' . $userAfter . ')';
                    break;
                case 'fk_regoin_invoice':
                    $regoinBefore = regoin::where('id_regoin', $dataBeforeUpdate[$key])->first()->name_regoin;
                    $regoinAfter = regoin::where('id_regoin', $dataAfterUpdate[$key])->first()->name_regoin;
                    $report[] = 'regoinName' . ': (' . $regoinBefore . ') TO (' . $regoinAfter . ') ';
                    break;
                    //users
                case 'type_administration':
                    $type_administrationBefore = managements::where('idmange', $dataBeforeUpdate[$key])->first()->name_mange;
                    $type_administrationAfter = managements::where('idmange', $dataAfterUpdate[$key])->first()->name_mange;
                    $report[] = 'type_administration' . ': (' . $type_administrationBefore . ') TO (' . $type_administrationAfter . ') ';
                    break;
                case 'fk_regoin':
                    $regoinBefore = regoin::where('id_regoin', $dataBeforeUpdate[$key])->first()->name_regoin;
                    $regoinAfter = regoin::where('id_regoin', $dataAfterUpdate[$key])->first()->name_regoin;
                    $report[] = 'regoinName' . ': (' . $regoinBefore . ') TO (' . $regoinAfter . ') ';
                    break;
                case 'type_level':
                    $levelBefore = levelModel::where('id_level', $dataBeforeUpdate[$key])->first()->name_level;
                    $levelAfter = levelModel::where('id_level', $dataAfterUpdate[$key])->first()->name_level;
                    $report[] = 'levelName' . ': (' . $levelBefore . ') TO (' . $levelAfter . ') ';
                    break;
                case 'nameMainCitiesAfter':
                    $nameMainCitiesAfter = implode(', ', $dataAfterUpdate[$key]);
                    $nameMainCitiesBefore = implode(', ', $nameMainCitiesBefor);
                    // if (!is_array($nameMainCitiesBefor)) {
                    //     $nameMainCitiesBefore = [$nameMainCitiesBefor];
                    // }

                    // $nameMainCitiesBefore = implode(', ', [$nameMainCitiesBefor]);
                default:
                    $report[] = $key . ': (' . $dataBeforeUpdate[$key] . ') TO (' . $dataAfterUpdate[$key] . ')';
                    break;
            }
        }

        return $report;
    }
}
