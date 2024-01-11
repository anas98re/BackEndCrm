<?php

namespace App\Console\Commands;

use App\Mail\sendupdatePermissionsReportToEmail as MailSendupdatePermissionsReportToEmail;
use App\Models\client_comment;
use App\Models\notifiaction;
use App\Models\privilageReport;
use App\Notifications\SendNotification;
use App\Services\TaskManangement\queriesService;
use App\Services\TaskManangement\TaskProceduresService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;


class sendupdatePermissionsReportToEmail extends Command
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-update-Permissions-Report-To-Email';

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
        $Engs = [
            'aya.ghoury@gmail.com',
            'sabo51051@gmail.com',
            'info@smartlifeco.com.tr'
        ];
        $yesterday = Carbon::yesterday()->startOfDay();
        $now = Carbon::now('Asia/Riyadh');

        $privilageReport = privilageReport::whereBetween('edit_date', [$yesterday, $now])->get();
        if (count($privilageReport) > 0) {
            foreach ($Engs as $Eng) {
                Mail::to($Eng)->send(new MailSendupdatePermissionsReportToEmail($privilageReport));
            }
        }
    }
}
