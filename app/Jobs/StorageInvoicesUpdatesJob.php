<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StorageInvoicesUpdatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $clientId;
    protected $dataBeforeUpdate;
    protected $dataAfterUpdate;
    protected $dateUpdate;
    protected $userId;
    /**
     * Create a new job instance.
     */
    public function __construct($clientId, $dataBeforeUpdate, $dataAfterUpdate, $dateUpdate, $userId)
    {
        $this->clientId = $clientId;
        $this->dataBeforeUpdate = $dataBeforeUpdate;
        $this->dataAfterUpdate = $dataAfterUpdate;
        $this->dateUpdate = $dateUpdate;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
