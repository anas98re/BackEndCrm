<?php

namespace App\Services;

use App\Constants;
use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\clients;
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
            ->where('fk_regoin', Constants::MARKETING_SALSE_ID)
            ->where('type_client', [Constants::NEGOTIATION, Constants::OFFER_PRICE])
            // ->where('is_check_marketing', 0)
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
            ->where('fk_regoin', Constants::MARKETING_SALSE_ID)
            ->where('type_client', [Constants::NEGOTIATION, Constants::OFFER_PRICE])
            // ->where('is_check_marketing', 0)
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
                        'تحويلات العملاء',
                        $messageWithCount2,
                        $messageWithCount2,
                        $userToken != null ? $userToken->token : null
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

    function generate_serialnumber($date_create, $index)
    {
        $day = Carbon::parse($date_create)->format('d');
        $m = Carbon::parse($date_create)->format('m');
        $yy = Carbon::parse($date_create)->format('y');
        $num = random_int(1111, 9999);
        $index1 = sprintf("%'.05d", $index);

        $serialNumber = $yy . $m . $day . $num . $index1;

        return $serialNumber;
    }

    function generate_serialnumber_InsertedClient($date_create_client)
    {
        // Assuming you have a 'Client' model and a 'clients' table
        $latestClient = clients::orderByDesc('id_clients')->first();

        if ($latestClient) {
            $date_create = $latestClient->date_create;
            $serialnum = $latestClient->SerialNumber;

            $yy = Carbon::parse($date_create)->format('y');
            $yy_client = Carbon::parse($date_create_client)->format('y');

            if ($yy_client != $yy) {
                $index = 1;
            } else {
                $index = intval(substr($serialnum, 11)) + 1;
            }
            // dd($index);
            // Assuming you have a 'generate_serialnumber' function
            $res = $this->generate_serialnumber($date_create_client, $index);

            return $res;
        }

        return null;
    }


    public function filterByNameClientOrEnterprise($query, $nameClient, $nameEnterprise)
    {
        // if ($nameClient) {
        //     $this->filterByNameClient($query, $nameClient);
        // }

        if ($nameEnterprise) {
            $this->filter_has_NameEnterprise($query, $nameEnterprise);
        }
    }

    private function filterByNameClient($query, $nameClient)
    {
        // Ensure the search term is properly encoded as UTF-8
        $searchTerm = mb_convert_encoding($nameClient, 'UTF-8', mb_detect_encoding($nameClient));

        // Split the search term to get the first word and the first three letters of the second word
        $searchTermParts = explode(' ', $searchTerm);
        $firstWord = $searchTermParts[0];
        $secondWordPrefix = isset($searchTermParts[1]) ? mb_substr($searchTermParts[1], 0, 3, 'UTF-8') : '';

        // Fetch results based on the first word and the first three letters of the second word for name_client
        $query->orWhere(function ($query) use ($firstWord, $secondWordPrefix) {
            $query->where('name_client', 'LIKE', $firstWord . ' ' . $secondWordPrefix . '%');
        });
    }

    private function filter_has_NameEnterprise($query, $nameEnterprise)
    {
        if ($nameEnterprise) {
            $query->orWhere(function ($query) use ($nameEnterprise) {
                $excludedWords = ['موسسة', 'مؤسسة', 'مؤسسه', 'جمعية', 'جمعيه'];
                $searchTerms = explode(' ', $nameEnterprise);

                // Exclude the first word if it matches any of the specified words
                if (count($searchTerms) > 1) {
                    $firstWord = array_shift($searchTerms);
                    if (in_array($firstWord, $excludedWords)) {
                        $searchTerms = array_values($searchTerms); // Re-index the array after removing the first word
                    }
                }

                // Apply the LIKE operator for each remaining word
                foreach ($searchTerms as $term) {
                    $query->where('name_enterprise', 'LIKE', '%' . $term . '%');
                }
            });
        }
    }

    private function filterByNameEnterprise($query, $nameEnterprise)
    {
        $excludedWords = ['موسسة', 'مؤسسة', 'مؤسسه', 'جمعية', 'جمعيه'];
        $excludeFirstWord = false;

        // Check if any of the excluded words are present in the name_enterprise
        foreach ($excludedWords as $excludedWord) {
            if (strpos($nameEnterprise, $excludedWord) !== false) {
                $excludeFirstWord = true;
                break;
            }
        }

        // Fetch results based on the adjusted query for name_enterprise
        $query->orWhere(function ($query) use ($nameEnterprise, $excludeFirstWord) {
            $searchTermParts = explode(' ', $nameEnterprise);
            $firstWord = isset($searchTermParts[0]) ? $searchTermParts[0] : '';
            $secondWordPrefix = isset($searchTermParts[1]) ? mb_substr($searchTermParts[1], 0, 3, 'UTF-8') : '';

            if (!$excludeFirstWord) {
                $query->where('name_enterprise', 'LIKE', $firstWord . ' ' . $secondWordPrefix . '%');
            } else {
                // If the first word is excluded, use the second word as the first word
                $Sec = isset($searchTermParts[1]) ? $searchTermParts[1] : '';
                $thirdWordPrefix = isset($searchTermParts[2]) ? mb_substr($searchTermParts[2], 0, 3, 'UTF-8') : '';
                $query->where('name_enterprise', 'LIKE', $Sec . ' ' . $thirdWordPrefix . '%');
            }
        });
    }
}
