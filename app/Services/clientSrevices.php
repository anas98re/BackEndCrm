<?php

namespace App\Services;

use App\Constants;
use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\notifiaction;
use App\Models\regoin;
use App\Models\tsks_group;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class clientSrevices extends JsonResponeService
{
    public function branchesIdsWithCountForTransformClientsFromMarketing($formattedDate)
    {
        return  DB::table('clients')
            ->where('ismarketing', 1)
            ->where('is_check_marketing', 0)
            ->whereDate('date_create', '>=', Carbon::createFromDate(2024, 1, 1)->endOfDay())
            ->where('date_create', '<', $formattedDate)
            ->select('fk_regoin', DB::raw('COUNT(*) as record_count'))
            ->groupBy('fk_regoin')
            ->pluck('record_count', 'fk_regoin');
    }

    public function sendNotificationsToBranchSupervisorsAndWhoHasPrivilage($branchesIdsWithNumberRepetitions)
    {

        info('Firebase notification: ' . json_encode($branchesIdsWithNumberRepetitions));
        $typeLevel = DB::table('privg_level_user')
            ->where('fk_privileg', Constants::NOTICE_OF_TRANSFERRING_MARKETING_CLIENTS_TO_MY_FIELD_ID)
            ->where('is_check', 1)
            ->pluck('fk_level');
        $brunshes = [];
        $messageWithRegion1 = [];
        foreach ($branchesIdsWithNumberRepetitions as $el => $value) {
            if ($el != Constants::ALL_BRUNSHES) {
                $regionId = regoin::where('id_regoin', $el)->first()->name_regoin;
                $message1 = ' هناك ? عميل في ! نحتاج تحويلهم الى ميداني';
                $messageWithCount1 = str_replace('?', $value, $message1);
                $messageWithRegion1[] = str_replace('!', $regionId, $messageWithCount1);
            }
        }

        $users = collect();
        $usersRegionIds = [];
        $currentKey = [];
        foreach ($branchesIdsWithNumberRepetitions as $key => $value) {
            if ($key != Constants::ALL_BRUNSHES) {
                $currentKey[] = $key;
            }
            // Now $currentKey contains the key
        }

        $usersQuery = DB::table('users as u')
            ->where(function ($query) use ($typeLevel, $currentKey) {
                $query->whereIn('u.fk_regoin', $currentKey)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->orWhere(function ($query) use ($typeLevel) {
                $query->where('u.fk_regoin', Constants::ALL_BRUNSHES)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->get();
        $users = $users->concat($usersQuery);

        foreach ($users as $user) {
            $userToken = DB::table('user_token')->where('fkuser', $user->id_user)
                ->where('token', '!=', null)
                ->latest('date_create')
                ->first();
            $usersRegionId = users::where('id_user', $user->id_user)->first()->fk_regoin;
            $message = implode("\n", $messageWithRegion1);
            if ($user->fk_regoin == Constants::ALL_BRUNSHES) {
                Notification::send(
                    null,
                    new SendNotification(
                        'تحويلات العملاء',
                        $message,
                        $message,
                        ($userToken != null ? $userToken->token : null)
                    )
                );

                notifiaction::create([
                    'message' => $message,
                    'type_notify' => 'checkClient',
                    'to_user' => $user->id_user,
                    'isread' => 0,
                    'data' => 'cls',
                    'from_user' => 1,
                    'dateNotify' => Carbon::now('Asia/Riyadh')
                ]);
            }
            $branchesArray = $branchesIdsWithNumberRepetitions->toArray();
            if (array_key_exists($usersRegionId, $branchesArray)) {
                if ($usersRegionId != Constants::ALL_BRUNSHES) {
                    $value = $branchesArray[$usersRegionId];

                    $regionName = regoin::where('id_regoin', $usersRegionId)->first()->name_regoin;
                    $message1 = ' يوجد ? عميل في ! نحتاج تحويلهم الى ميداني';
                    $messageWithCount2 = str_replace('?', $value, $message1);
                    $messageWithRegion2 = str_replace('!', $regionName, $messageWithCount2);
                    Notification::send(
                        null,
                        new SendNotification(
                            'تحويلات العملاء',
                            $messageWithRegion2,
                            $messageWithRegion2,
                            ($userToken != null ? $userToken->token : null)
                        )
                    );

                    notifiaction::create([
                        'message' => $messageWithRegion2,
                        'type_notify' => 'checkClient',
                        'to_user' => $user->id_user,
                        'isread' => 0,
                        'data' => 'ccl',
                        'from_user' => 1,
                        'dateNotify' => Carbon::now('Asia/Riyadh')
                    ]);
                }
            }
        }
        return $users;
    }

    public function sendNotificationsToResponsapilUserOfClient($formattedDate)
    {
        $userIds = DB::table('clients')
            ->where('ismarketing', 1)
            ->where('is_check_marketing', 0)
            ->whereDate('date_create', '>=', Carbon::createFromDate(2024, 1, 1)->endOfDay())
            ->where('date_create', '<', $formattedDate)
            ->pluck('fk_user');

        $duplicates = array_count_values($userIds->toArray());

        foreach ($duplicates as $key => $value) {
            $userToken = DB::table('user_token')->where('fkuser', $key)
                ->where('token', '!=', null)
                ->latest('date_create')
                ->first();

            $message2 = ' لديك ? عميل نحتاج تحويلهم الى ميداني ';

            $messageWithCount2 = str_replace('?', $value, $message2);
            if ($userToken) {
                Notification::send(
                    null,
                    new SendNotification(
                        'تعليقات العملاء',
                        $messageWithCount2,
                        $messageWithCount2,
                        ($userToken != null ? $userToken->token : null)
                    )
                );

                notifiaction::create([
                    'message' => $messageWithCount2,
                    'type_notify' => 'checkClient',
                    'to_user' => $key,
                    'isread' => 0,
                    'data' => 'ccl',
                    'from_user' => 1,
                    'dateNotify' => Carbon::now('Asia/Riyadh')
                ]);
            }
        }
    }
}
