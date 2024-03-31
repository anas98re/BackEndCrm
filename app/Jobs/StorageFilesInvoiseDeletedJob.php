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

class StorageFilesInvoiseDeletedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $modelId;
    protected $model;
    protected $userId;
    protected $update_source;
    protected $routePattern;
    protected $description;

    /**
     * Create a new job instance.
     */
    public function __construct(
        $modelId,
        $model,
        $userId,
        $update_source,
        $routePattern,
        $description,
    ) {
        $this->modelId = $modelId;
        $this->model = $model;
        $this->userId = $userId;
        $this->update_source = $update_source;
        $this->routePattern = $routePattern;
        $this->description = $description;
    }

    public function handle(): void
    {
        info(2);
        $dateUpdate = Carbon::now('Asia/Riyadh')->toDateTimeString();

        ChangeLog::create([
            'model' => $this->model,
            'action' => 'updated',
            'changesData' => $reportMessage,
            'description' => $this->description,
            'user_id' => (int) $this->userId,
            'model_id' => $this->modelId,
            'edit_date' => $dateUpdate,
            'source' => $this->update_source,
            'route' => $this->routePattern,
            'afterApprove' => null,
            'ip' => null
        ]);
    }
}
