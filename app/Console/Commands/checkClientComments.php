<?php

namespace App\Console\Commands;

use App\Http\Controllers\TaskProceduresController;
use App\Models\client_comment;
use App\Models\notifiaction;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\TaskManangement\queriesService;
use App\Services\TaskManangement\TaskProceduresService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class checkClientComments extends Command
{
    private $MyService;
    private $MyQueriesService;
    private $myController;

    public function __construct(
        TaskProceduresService $MyService,
        queriesService $MyQueriesService,
        TaskProceduresController $myController
    ) {
        $this->MyService = $MyService;
        $this->MyQueriesService = $MyQueriesService;
        $this->myController = $myController;
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-client-comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->myController->addTaskWhenThereIsNoUpdateToTheLatestClientUpdatesFor5Days();
    }
}
