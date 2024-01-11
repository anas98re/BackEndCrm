<?php

namespace App\Console\Commands;

use App\Constants;
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
        try {
            DB::beginTransaction();
            $updateClientData = DB::table('clients')
                ->where('ismarketing', 1)
                ->where('is_check_marketing', 0)
                ->whereDate('date_create', '>=', Carbon::createFromDate(2024, 1, 1)->endOfDay())
                ->where('date_create', '<', Carbon::now('Asia/Riyadh')->subDays(8)->format('Y-m-d H:i:s'))
                ->update([
                    'oldSourceClient' => DB::raw('sourcclient'), // Assuming 'oldSourceClient' is the column where you want to store old values
                    'sourcclient' => Constants::MAIDANI,
                    'is_check_marketing' => 1,
                ]);

            // 'oldSourceClient' will now contain the old values of 'sourcclient'
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

}
