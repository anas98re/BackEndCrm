<?php

namespace App\Console\Commands;

use App\Constants;
use App\Http\Controllers\ClientsController;
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

class transformClientsFromMarketingIfOverrideLimit8Days extends Command
{
    private $MyController;

    public function __construct(ClientsController $MyController)
    {
        $this->MyController = $MyController;
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:transform-Clients-From-Marketing-If-Override-Limit-8Days';

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
        $this->MyController->transformClientsFromMarketingIfOverrideLimit8Days();
    }

}
