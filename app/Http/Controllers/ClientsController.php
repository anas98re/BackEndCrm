<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\clients;
use App\Http\Requests\StoreclientsRequest;
use App\Http\Requests\UpdateclientsRequest;
use App\Mail\sendStactictesConvretClientsToEmail;
use App\Mail\sendStactictesConvretClientsTothabetEmail;
use App\Models\client_comment;
use App\Models\convertClintsStaticts;
use App\Models\notifiaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;
use App\Services\clientSrevices;
use Illuminate\Support\Facades\Mail;

class ClientsController extends Controller
{
    private $MyService;

    public function __construct(clientSrevices $MyService)
    {
        $this->MyService = $MyService;
    }
    public function editClientByTypeClient($id_clients, Request $request)
    {
        try {
            DB::beginTransaction();
            if ($request->type_client == 'عرض سعر') {
                $updateClientData = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->update(
                        [
                            'type_client' => $request->type_client,
                            'date_changetype' => Carbon::now('Asia/Riyadh'),
                            'offer_price' => $request->offer_price,
                            'date_price' => $request->date_price,
                            'user_do' => $request->id_user,
                        ]
                    );
            }
            if ($request->type_client == 'تفاوض') {
                $updateClientData = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->update(
                        [
                            'type_client' => $request->type_client,
                            'date_changetype' => Carbon::now('Asia/Riyadh'),
                            'offer_price' => $request->offer_price,
                            'date_price' => $request->date_price,
                            'user_do' => $request->id_user,
                        ]
                    );
            }
            if ($request->type_client == 'مستبعد') {
                $updateClientData = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->update(
                        [
                            'type_client' => 'معلق استبعاد',
                            'date_changetype' => Carbon::now('Asia/Riyadh'),
                            'fk_rejectClient' => $request->fk_rejectClient,
                            'reason_change' => $request->reason_change,
                            'user_do' => $request->id_user,
                        ]
                    );
                //add comment to client comment table.
                $lastId = DB::table('client_comment')
                    ->orderBy('id_comment', 'desc')
                    ->value('id_comment');

                $idComment = $lastId + 1;
                $comment = new client_comment();
                $comment->id_comment = $idComment;
                $comment->content = $request->reason_change;
                $comment->date_comment = Carbon::now('Asia/Riyadh');
                $comment->fk_client = $id_clients;
                $comment->fk_user = $request->id_user;
                $comment->save();

                //send notification to supervisor salse for client's brunch
                $brunchClient = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->first()
                    ->fk_regoin;

                $usersId = DB::table('users')
                    ->where('fk_regoin', $brunchClient)
                    ->where('isActive', 1)
                    ->where('type_level', 14)
                    ->pluck('id_user');

                $nameClient = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->first()->name_enterprise;

                $message1 = 'العميل ? يحتاج لموافقة على الاستبعاد';
                $messageNotifi = str_replace('?', $nameClient, $message1);

                foreach ($usersId as $Id) {
                    $userToken = DB::table('user_token')->where('fkuser', $Id)
                        ->where('token', '!=', null)
                        ->latest('date_create')
                        ->first();
                    Notification::send(
                        null,
                        new SendNotification(
                            $messageNotifi,
                            $request->type_client,
                            'Edit',
                            ($userToken != null ? $userToken->token : null)
                        )
                    );

                    notifiaction::create([
                        'message' => $messageNotifi,
                        'type_notify' => 'تحديث عميل',
                        'to_user' => $Id,
                        'isread' => 0,
                        'data' => $id_clients,
                        'from_user' => 1,
                        'dateNotify' => Carbon::now('Asia/Riyadh')
                    ]);
                }
            }
            $ClientData = DB::table('clients')
                ->where('id_clients', $id_clients)->first();
            DB::commit();
            return $this->sendResponse($ClientData, 'updated');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function appproveAdmin($id_clients, Request $request)
    {
        if ($request->isAppprove) {
            $updateClientData = DB::table('clients')
                ->where('id_clients', $id_clients)
                ->update(
                    [
                        'type_client' => 'مستبعد',
                        'date_changetype' => Carbon::now('Asia/Riyadh'),
                    ]
                );
        } else {
            $updateClientData = DB::table('clients')
                ->where('id_clients', $id_clients)
                ->update(
                    [
                        'type_client' => 'تفاوض',
                        'date_changetype' => Carbon::now('Asia/Riyadh'),
                    ]
                );
        }
        $ClientData = DB::table('clients')
            ->where('id_clients', $id_clients)->first();
        return $this->sendResponse($ClientData, 'Done');
    }

    public function transformClientsFromMarketingIfOverrideLimit8Days()
    {
        try {
            DB::beginTransaction();
            $sevenWorkingDaysAgo = Carbon::now('Asia/Riyadh');

            // Adjust the date to exclude Fridays and Saturdays and go back 7 working days
            for ($i = 0; $i < 7; $i++) {
                do {
                    $sevenWorkingDaysAgo->subDay();
                } while ($sevenWorkingDaysAgo->isFriday() || $sevenWorkingDaysAgo->isSaturday());
            }

            $formattedDate = $sevenWorkingDaysAgo->format('Y-m-d H:i:s');
            $branchesIdsWithNumberRepetitions = $this->MyService
                ->branchesIdsWithCountForTransformClientsFromMarketing($formattedDate);

            $this->MyService
                ->sendNotificationsToResponsapilUserOfClient($formattedDate);

            // $updateClientData = DB::table('clients')
            //     ->where('ismarketing', 1)
            //     ->where('is_check_marketing', 0)
            //     ->whereDate('date_create', '>=', Carbon::createFromDate(2024, 1, 1)->endOfDay())
            //     ->where('date_create', '<', $formattedDate)
            //     ->update([
            //         // 'oldSourceClient' => DB::raw('sourcclient'), // Assuming 'oldSourceClient' is the column where you want to store old values
            //         // 'sourcclient' => Constants::MAIDANI,
            //         'is_check_marketing' => 1,
            //     ]);

            $this->MyService
                ->sendNotificationsToBranchSupervisorsAndWhoHasPrivilage($branchesIdsWithNumberRepetitions);


            // 'oldSourceClient' will now contain the old values of 'sourcclient'
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function addClient(StoreclientsRequest $request)
    {
        $serialnumber =
            $this->MyService->generate_serialnumber_InsertedClient(
                $request->input('date_create')
            );

        $data = $request->all();
        $data['SerialNumber'] = $serialnumber;

        clients::create($data);

        return response()->json(['message' => 'Client created successfully']);
    }


    public function SimilarClientsNames(Request $request)
    {
        $selectFields = [
            'name_client',
            'name_enterprise',
            'phone',
            'id_clients',
            'date_create',
            'SerialNumber'
        ];

        $query = clients::query();

        if ($request->has('phone') && !empty($request->phone)) {
            // Create a separate instance of the query to execute only the phone condition
            $phoneQuery = clients::query()->where('phone', $request->phone)->select($selectFields)->get();

            // Check if there are results for phone query
            if ($phoneQuery->count() > 0) {
                return response()->json($phoneQuery);
            }
        }

        // Continue with name_client or name_enterprise condition
        if ($request->has('name_client') || $request->has('name_enterprise')) {
            $nameClient = $request->input('name_client');
            $nameEnterprise = $request->input('name_enterprise');

            $this->MyService->filterByNameClientOrEnterprise($query, $nameClient, $nameEnterprise);
        }

        // Get results for name_client or name_enterprise condition
        $results = $query->select($selectFields)->get();

        return response()->json($results);
    }


    // public function SimilarClientsNames(Request $request)
    // {
    //     $query = clients::query();
    //     $check = 0;
    //     if ($request->has('phone')) {
    //         $query->orWhere('phone', $request->phone);
    //         $check = 1;
    //     }
    //     if (!$check) {
    //         $nameClientExists = $request->has('name_client');
    //         $nameEnterpriseExists = $request->has('name_enterprise');

    //         if ($nameClientExists || $nameEnterpriseExists) {
    //             if ($nameClientExists) {
    //                 // Ensure the search term is properly encoded as UTF-8
    //                 $searchTermForNameClient = mb_convert_encoding(
    //                     $request->name_client,
    //                     'UTF-8',
    //                     mb_detect_encoding($request->name_client)
    //                 );

    //                 // Split the search term to get the first word and the first three letters of the second word
    //                 $searchTermNameClientParts = explode(' ', $searchTermForNameClient);
    //                 $firstWordNameClient = $searchTermNameClientParts[0];
    //                 $secondWordPrefixNameClient = isset(
    //                     $searchTermNameClientParts[1]
    //                 ) ? mb_substr($searchTermNameClientParts[1], 0, 3, 'UTF-8') : '';

    //                 // Fetch results based on the first word and the first three letters of the second word
    //                 $query->orWhere(function ($query) use ($firstWordNameClient, $secondWordPrefixNameClient) {
    //                     $query->where('name_client', 'LIKE', $firstWordNameClient . ' ' . $secondWordPrefixNameClient . '%');
    //                 });
    //             }

    //             if ($nameEnterpriseExists) {
    //                 $excludedWordsName_enterprise = ['موسسة', 'مؤسسة', 'مؤسسه', 'جمعية', 'جمعيه'];
    //                 $excludeFirstWord = false;

    //                 foreach ($excludedWordsName_enterprise as $excludedWord) {
    //                     if (strpos($request->name_enterprise, $excludedWord) !== false) {
    //                         $excludeFirstWord = true;
    //                         break;
    //                     }
    //                 }

    //                 // Fetch results based on the adjusted query
    //                 $query->orWhere(function ($query) use ($request, $excludeFirstWord) {
    //                     $searchTermParts = explode(' ', $request->name_enterprise);
    //                     $firstWord = isset($searchTermParts[0]) ? $searchTermParts[0] : '';
    //                     $secondWordPrefix = isset($searchTermParts[1]) ? mb_substr($searchTermParts[1], 0, 3, 'UTF-8') : '';

    //                     if (!$excludeFirstWord) {
    //                         $query->where('name_enterprise', 'LIKE', $firstWord . ' ' . $secondWordPrefix . '%');
    //                     } else {
    //                         // If the first word is excluded, use the second word as the first word
    //                         $Sec = isset($searchTermParts[1]) ? $searchTermParts[1] : '';
    //                         $thirdWordPrefix = isset($searchTermParts[2]) ? mb_substr($searchTermParts[2], 0, 3, 'UTF-8') : '';
    //                         $query->where('name_enterprise', 'LIKE', $Sec . ' ' . $thirdWordPrefix . '%');
    //                     }
    //                 });
    //             }
    //         }


    //     }

    //     $results = $query->select(
    //         'name_client',
    //         'name_enterprise',
    //         'phone',
    //         'id_clients',
    //         'date_create',
    //         'SerialNumber'
    //     )->get();

    //     return response()->json($results);
    // }

    // if ($request->has('name_enterprise')) {
    //     // Adjust the query to exclude certain words if present in the first word
    //     $excludedWords = ['موسسة', 'مؤسسة', 'مؤسسه', 'جمعية', 'جمعيه'];
    //     $excludeFirstWord = false;

    //     foreach ($excludedWords as $excludedWord) {
    //         if (strpos($request->name_enterprise, $excludedWord) !== false) {
    //             $excludeFirstWord = true;
    //             break;
    //         }
    //     }

    //     // Fetch results based on the adjusted query
    //     $query->orWhere(function ($query) use ($request, $excludeFirstWord) {
    //         $searchTermParts = explode(' ', $request->name_enterprise);
    //         $firstWord = isset($searchTermParts[0]) ? $searchTermParts[0] : '';
    //         $secondWordPrefix = isset($searchTermParts[1]) ? mb_substr($searchTermParts[1], 0, 3, 'UTF-8') : '';

    //         if (!$excludeFirstWord) {
    //             $query->where('name_enterprise', 'LIKE', $firstWord . ' ' . $secondWordPrefix . '%');
    //         } else {
    //             // If the first word is excluded, use the second word as the first word
    //             $Sec = isset($searchTermParts[1]) ? $searchTermParts[1] : '';
    //             $thirdWordPrefix = isset($searchTermParts[2]) ? mb_substr($searchTermParts[2], 0, 3, 'UTF-8') : '';
    //             $query->where('name_enterprise', 'LIKE', $Sec . ' ' . $thirdWordPrefix . '%');
    //         }
    //     });
    // }





    // public function SimilarClientsNames1(Request $request)
    // {
    //     $query = clients::query();

    //     if ($request->has('name_client')) {
    //         // Ensure the search term is properly encoded as UTF-8
    //         $searchTerm = mb_convert_encoding($request->name_client, 'UTF-8', mb_detect_encoding($request->name_client));

    //         // Split the search term to get the first word and the first three letters of the second word
    //         $searchTermParts = explode(' ', $searchTerm);
    //         $firstWord = $searchTermParts[0];
    //         $secondWordPrefix = isset($searchTermParts[1]) ? mb_substr($searchTermParts[1], 0, 3, 'UTF-8') : '';

    //         // Fetch results based on the first word match
    //         $results = $query->where('name_client', 'LIKE', $firstWord . '%')->get();

    //         // Filter results to ensure uniqueness based on the first word and the first three letters of the second word
    //         $uniqueResults = collect([]);
    //         $processed = [];
    //         foreach ($results as $result) {
    //             $resultFirstWord = explode(' ', $result->name_client)[0];
    //             $resultSecondWordPrefix = isset(explode(' ', $result->name_client)[1]) ? mb_substr(explode(' ', $result->name_client)[1], 0, 3, 'UTF-8') : '';

    //             $key = $resultFirstWord . '_' . $resultSecondWordPrefix;
    //             if (!in_array($key, $processed)) {
    //                 $uniqueResults->push($result);
    //                 $processed[] = $key;
    //             }
    //         }

    //         // Select specific fields from unique results
    //         $selectedResults = $uniqueResults->map(function ($result) {
    //             return [
    //                 'name_client' => $result->name_client,
    //                 'name_enterprise' => $result->name_enterprise,
    //                 'phone' => $result->phone,
    //                 'id_clients' => $result->id_clients,
    //                 'date_create' => $result->date_create,
    //                 'SerialNumber' => $result->SerialNumber,
    //             ];
    //         });

    //         return response()->json($selectedResults);
    //     }

    //     if ($request->has('name_enterprise')) {
    //         // Adjust the query to exclude certain words if present in the first word
    //         $excludedWords = ['موسسة', 'مؤسسة', 'مؤسسه'];
    //         $excludeFirstWord = false;
    //         foreach ($excludedWords as $excludedWord) {
    //             if (strpos($request->name_enterprise, $excludedWord) !== false) {
    //                 $excludeFirstWord = true;
    //                 break;
    //             }
    //         }

    //         // Fetch results based on the adjusted query
    //         $query->where(function ($query) use ($request, $excludeFirstWord) {
    //             if (!$excludeFirstWord) {
    //                 $query->orWhere('name_enterprise', 'LIKE', '%' . $request->name_enterprise . '%');
    //             } else {
    //                 $query->orWhereRaw("CONCAT(' ', name_client) LIKE ?", ['% ' . $request->name_enterprise . '%']);
    //             }
    //         });
    //     }

    //     if ($request->has('phone')) {
    //         $query->orWhere('phone', 'LIKE', '%' . $request->phone . '%');
    //     }

    //     $results = $query->select(
    //         'name_client',
    //         'name_enterprise',
    //         'phone',
    //         'id_clients',
    //         'date_create',
    //         'SerialNumber'
    //     )->get();

    //     return response()->json($results);
    // }






    // Function to calculate similarity score
    private function calculateSimilarityScore($client, $request)
    {
        $similarityScore = 0;

        // Split client name into parts
        $clientNameParts = explode(' ', $client->name_client);
        // Take the first word
        $clientFirstName = $clientNameParts[0];
        // Take the second word and get the first three letters (if exists)
        $clientSecondPart = isset($clientNameParts[1]) ? substr($clientNameParts[1], 0, 3) : '';

        // Split request name into parts
        $requestNameParts = explode(' ', $request->name_client);
        // Take the first word
        $requestFirstName = $requestNameParts[0];
        // Take the second word and get the first three letters (if exists)
        $requestSecondPart = isset($requestNameParts[1]) ? substr($requestNameParts[1], 0, 3) : '';

        // Calculate similarity score for the first word of the name
        similar_text($clientFirstName, $requestFirstName, $similarityScore);
        // Calculate similarity score for the first three letters of the second word
        similar_text($clientSecondPart, $requestSecondPart, $similarityScore);

        // Calculate similarity score for other attributes (name_enterprise and phone)
        similar_text($client->name_enterprise, $request->name_enterprise, $similarityScore);
        similar_text($client->phone, $request->phone, $similarityScore);

        return $similarityScore;
    }


    // to test ..
    private function getPluckColumn(Request $request)
    {
        return $request->has('name_client')
            ? 'name_client'
            : ($request->has('name_enterprise') ? 'name_enterprise' : 'phone');
    }

    public function convertClientsFromAnEmployeeToEmployee(Request $request)
    {
        try {
            DB::beginTransaction();
            $oldUserId = $request->oldUserId;
            $newUserId = $request->newUserId;
            $Count = DB::table('clients')->where('fk_user', $oldUserId)
                ->where(function ($query) {
                    $query->where('type_client', 'مستبعد')
                        ->orWhere('type_client', 'تفاوض');
                })
                ->whereYear('date_create', 2023)->count();

            convertClintsStaticts::create([
                'numberOfClients' => $Count != 0  ? $Count : 0,
                'convert_date' => Carbon::now('Asia/Riyadh'),
                'oldUserId' => $oldUserId,
                'newUserId' => $newUserId,
            ]);
            // Perform the update and get the number of affected rows
            $affectedRows = DB::table('clients')
                ->where('fk_user', $oldUserId)
                ->where(function ($query) {
                    $query->where('type_client', 'مستبعد')
                        ->orWhere('type_client', 'تفاوض');
                })
                ->whereYear('date_create', 2023)
                ->update(['fk_user' => $newUserId]);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function sendStactictesConvretClientsToEmail(Request $request)
    {
        $ClintsStaticts = convertClintsStaticts::with(['oldUser', 'newUser'])->get();
        Mail::to($request->email)->send(new sendStactictesConvretClientsToEmail($ClintsStaticts));
    }
}
