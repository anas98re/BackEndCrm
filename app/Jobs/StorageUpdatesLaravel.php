<?php

namespace App\Jobs;

use App\Models\activity_type;
use App\Models\agent;
use App\Models\ChangeLog;
use App\Models\city;
use App\Models\company;
use App\Models\level;
use App\Models\levelModel;
use App\Models\managements;
use App\Models\participate;
use App\Models\regoin;
use App\Models\updatesReport;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StorageUpdatesLaravel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $modelId;
    protected $model;
    protected $dataBeforeUpdate;
    protected $dataAfterUpdate;
    protected $dateUpdate;
    protected $userId;
    protected $update_source;
    protected $routePattern;
    protected $description;
    protected $nameMainCitiesBefor;
    protected $isApprove;
    /**
     * Create a new job instance.
     */
    public function __construct(
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
        $this->modelId = $modelId;
        $this->model = $model;
        $this->dataBeforeUpdate = $dataBeforeUpdate;
        $this->dataAfterUpdate = $dataAfterUpdate;
        $this->dateUpdate = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $this->userId = $userId;
        $this->update_source = $update_source;
        $this->routePattern = $routePattern;
        $this->description = $description;
        $this->nameMainCitiesBefor = $nameMainCitiesBefor;
        $this->isApprove = $isApprove;
    }

    public function handle(): void
    {
        info(2);
        $dataBeforeUpdate = $this->dataBeforeUpdate;
        $dataAfterUpdate = $this->dataAfterUpdate;
        info($dataBeforeUpdate);
        info($dataAfterUpdate);
        $differences = array_diff_assoc(json_decode($dataAfterUpdate), json_decode($dataBeforeUpdate));

        if ($differences) {
            info(3);
            $report = $this->generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate);
            info(4);
            $reportMessage = implode("\n", $report);

            ChangeLog::create([
                'model' => $this->model,
                'action' => 'updated',
                'changesData' => $reportMessage,
                'description' => $this->description,
                'user_id' => (int) $this->userId,
                'model_id' => $this->modelId,
                'edit_date' => $this->dateUpdate,
                'source' => $this->update_source,
                'route' => $this->routePattern,
                'afterApprove' => $this->isApprove,
                'ip' => null
            ]);
        }
    }

    private function generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate)
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
                    $cityBefore = city::where('id_city', $dataBeforeUpdate[$key])->first()->name_city;
                    $cityAfter = city::where('id_city', $dataAfterUpdate[$key])->first()->name_city;
                    $report[] = 'city: (' . $cityBefore . ') TO (' . $cityAfter . ')';
                    break;
                case 'activity_type_fk':
                    $activityBefore = activity_type::where('id_activity_type', $dataBeforeUpdate[$key])->first()->name_activity_type;
                    $activityAfter = activity_type::where('id_activity_type', $dataAfterUpdate[$key])->first()->name_activity_type;
                    $report[] = 'activity_type: (' . $activityBefore . ') TO (' . $activityAfter . ')';
                    break;
                case 'presystem':
                    $presystemBefore = company::where('id_Company', $dataBeforeUpdate[$key])->first()->name_company;
                    $presystemAfter = company::where('id_Company', $dataAfterUpdate[$key])->first()->name_company;
                    $report[] = 'presystem: (' . $presystemBefore . ') TO (' . $presystemAfter . ')';
                    break;
                    // Invoices
                case 'participate_fk':
                    $participateBefore = 'not_found';
                    $participateAfter = 'not_found';
                    if ($dataBeforeUpdate[$key]) {
                        $participateBefore = participate::where('id_participate', $dataBeforeUpdate[$key])->first()->name_participate;
                    }
                    if ($dataAfterUpdate[$key]) {
                        $participateAfter = participate::where('id_participate', $dataAfterUpdate[$key])->first()->name_participate;
                    }
                    $report[] = 'participate_fk: (' . $participateBefore . ') TO (' . $participateAfter . ')';
                    break;
                case 'fk_agent':
                    $agentBefore = 'not_found';
                    $agentAfter = 'not_found';
                    if ($dataBeforeUpdate[$key]) {
                        $agentBefore = agent::where('id_agent', $dataBeforeUpdate[$key])->first()->name_agent;
                    }
                    if ($dataAfterUpdate[$key]) {
                        $agentAfter = agent::where('id_agent', $dataAfterUpdate[$key])->first()->name_agent;
                    }
                    $report[] = 'fk_agent: (' . $agentBefore . ') TO (' . $agentAfter . ')';
                    break;
                case 'type_seller':
                    $typeSellerOptions = ['موزع', 'وكيل', 'متعاون', 'موظف'];
                    $typeSellerAfter = $typeSellerOptions[$dataAfterUpdate[$key]] ?? 'موظف';
                    $typeSellerBefore = $typeSellerOptions[$dataBeforeUpdate[$key]] ?? 'موظف';
                    $report[] = 'type_seller: (' . $typeSellerBefore . ') TO (' . $typeSellerAfter . ')';
                    break;
                case 'fk_idUser':
                    $userBefore = users::where('id_user', $dataBeforeUpdate[$key])->first()->name_user;
                    $userAfter = users::where('id_user', $dataAfterUpdate[$key])->first()->name_user;
                    $report[] = 'fk_idUser: (' . $userBefore . ') TO (' . $userAfter . ')';
                    break;
                default:
                    $report[] = $key . ': (' . $dataBeforeUpdate[$key] . ') TO (' . $dataAfterUpdate[$key] . ')';
                    break;
            }
        }

        return $report;
    }

    private function getObjectDifferences($object1, $object2)
    {
        $differences = array_diff_assoc(get_object_vars($object1), get_object_vars($object2));

        foreach ($differences as $property => $value) {
            $differences[$property] = [
                'before' => isset($object2->$property) ? $object2->$property : null,
                'after' => $value,
            ];
        }

        return $differences;
    }
}
