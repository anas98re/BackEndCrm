<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Models\clients;
use App\Models\client_communication;
use App\Models\client_invoice;
use App\Models\config_table;
use App\Models\notifiaction;
use App\Models\task;
use App\Models\user_token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;
use App\Services\TaskManangement\queriesService;
use App\Services\TaskManangement\TaskProceduresService;
use App\Services\TaskManangement\TaskService;
use Exception;
use App\Http\Requests\Storeclient_communicationRequest;
use App\Http\Requests\Updateclient_communicationRequest;
use App\Models\clientCommentMention;
use App\Models\ClientCommunication;
use App\Models\Regoin;
use App\Models\Users;
use App\Models\ClientInvoice;



class ClientCommunicationController extends Controller
{
    // ...
    private $TaskService;

    public function __construct(TaskService $TaskService)
    {
        $this->TaskService = $TaskService;
    }
    public function setDateInstall(Request $request, string $id_invoice)
    {
        DB::beginTransaction();
        $data = $request->all();
        try {
            $invoice = client_invoice::where('id_invoice', $id_invoice)?->first();
            $client = $invoice->client;
            $updateArray = array();
            $updateArray['userinstall'] = auth()->user()->id_user;
            $updateArray['clientusername'] = $data['clientusername'];
            $updateArray['isdoneinstall'] = 1;
            $updateArray['dateinstall_done'] = Carbon::now();

            $invoice->update($updateArray);

            $time = config_table::where('name_config', 'period_commincation2')
                ->first()->value_config;

            $insertArray = array();
            $insertArray['fk_client'] = $client->id_clients;
            $insertArray['date_next'] = Carbon::parse(Carbon::now())->addDays($time);
            $insertArray['id_invoice'] = $id_invoice;
            $insertArray['type_communcation'] = 'تركيب';
            $insertArray['type_install'] = 1;
            $insertArray['fk_user'] = $this->get_fk_user_communication($client->id_clients);

            client_communication::create($insertArray);

            // $this->update_fkuser_communication($client->id_clients);

            $time = config_table::where('name_config', 'period_commincation3')
                ->first()->value_config;
            $carbonDatetime = Carbon::parse(Carbon::now())->addDays($time);
            $date_next = $carbonDatetime;

            $arrJson_result = $this->getcommunication_repeatcheck($client->id_clients);
            if (!$arrJson_result) // [] => false, ![] => true
                $this->addcommunication($client->id_clients, $date_next);


            $fk_regoin = $client->fk_regoin;
            $fkcountry = $client->regoin?->fk_country;
            $id_users =  getIdUsers($fk_regoin, 21, $fkcountry);
            $array2user = getIdUsersRegoin($fkcountry, 21, $client->id_clients);
            $id_users = array_merge($id_users->toArray(), $array2user->toArray());


            $dataIn = [
                'idInvoice' => $id_invoice,
                'fkIdClient' => $client->id_clients
            ];
            $this->TaskService->afterInstallClient($dataIn);

            $name_enterprise = $client->name_enterprise;
            $nameuserinstall = auth()->user()->nameUser;
            $title = "تم التركيب";
            $titlenameapprove = "الموظف الذي ركب للعميل ";
            $id_users = collect($id_users)->unique();
            $message = "$titlenameapprove $name_enterprise هو\r $nameuserinstall";
            foreach ($id_users as $user_id) {
                $userToken = user_token::where('fkuser', $user_id)
                    ->where('token', '!=', null)
                    ->latest('date_create')
                    ->first();

                Notification::send(
                    null,
                    new SendNotification(
                        $title,
                        $message,
                        $message,
                        ($userToken != null ? $userToken->token : null)
                    )
                );

                notifiaction::create([
                    'message' => $message,
                    'type_notify' => 'Install',
                    'to_user' => $user_id,
                    'isread' => 0,
                    'data' => $client->id_clients,
                    'from_user' => auth()->user()->id_user,
                    'dateNotify' => Carbon::now('Asia/Riyadh')
                ]);
            }

            $invoice = client_invoice::where('id_invoice', $id_invoice)->first();
            $arrJson = new InvoiceResource($invoice);

            $resJson = array("result" => "success", "code" => "200", "message" => $arrJson);

            DB::commit();
            return response()->json($resJson);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    protected function update_fkuser_communication($fkIdclient)
    {
        $result = client_communication::where('fk_client', $fkIdclient)
            ->whereNotNull('fk_user')
            ->where('type_communcation', 'ترحيب')
            ->orderBy('date_communication', 'desc')
            ?->first();

        if (!is_null($result)) {
            $communications = client_communication::where('fk_client', $fkIdclient)
                ->whereNull('date_communication')
                ->get();
            foreach ($communications as $communication)
                $communication->update(['fk_user' => $result->fk_user]);
        }
    }

    protected function getcommunication_repeatcheck($fkclient)
    {
        return client_communication::where('fk_client', $fkclient)
            ->where('type_communcation', 'دوري')
            ->orderBy('date_communication', 'desc')
            ->get();
    }

    protected function addcommunication($fk_client, $date_next)
    {
        client_communication::create([
            'fk_client' => $fk_client,
            'type_communcation' => 'دوري',
            'date_next' => $date_next,
        ]);


        $this->update_fkuser_communication($fk_client);
    }
    protected function get_fk_user_communication($fk_client)
    {
        return client_communication::where('fk_client', $fk_client)
            ->whereNotNull('fk_user')
            ->where('type_communcation', 'ترحيب')
            ->orderBy('date_communication', 'desc')
            ?->first()
            ?->fk_user;
    }



    // ......
    public function updateCommunication(Request $request)
    {
        $id_communication = request()->query('id_communication');
        $id_invoice = $request->input('id_invoice');

        //Tasks ...
        $lastCommunication = client_communication::latest('id_communication')->first();
        $last_idCommuncation2 = $lastCommunication->id_communication;
        $data = [
            'last_idCommuncation2' => $last_idCommuncation2,
            'id_communication' => $id_communication,
            'iduser_updateed' => auth()->user()->id_user,
            'idInvoice' => $id_invoice
        ];
        $this->TaskService->closeWelcomeTaskAfterUpdateCommunication($data);
        $this->TaskService->closeTaskafterCommunicateWithClient($data);

        $communication = client_communication::with(['client', 'user', 'invoice'])
            ->where('id_communication', $id_communication)
            ->first();

        $type = $request->input('type');
        $updated = $request->input('updated');
        if ($communication) {
            if (!$updated) {
                $communication->date_next = $request->input('date_next', $communication->date_next);
            } else {
                $communication->user_update = auth()->user()->id_user;
            }
            $communication->date_communication =  Carbon::now();
            $communication->type_communcation = $request->input('type_communcation', $communication->type_communcation);
            $communication->fk_user = auth()->user()->id_user;
            $communication->result = $request->input('result', $communication->result);
            $communication->rate = $request->input('rate', $communication->rate) == '0,0' ?  null :
                $request->input('rate', $communication->rate);
            $communication->number_wrong = $request->input('number_wrong', $communication->number_wrong);
            $communication->client_repeat = $request->input('client_repeat', $communication->client_repeat);
            $communication->is_suspend = $request->input('is_suspend', $communication->is_suspend);
            $communication->isRecommendation = $request->input('isRecommendation', $communication->isRecommendation);
            $communication->is_visit = $request->input('is_visit', $communication->is_visit);

            $communication->save();
        }


        $data['communication'] = $communication;
        $data = $this->getCommunicationById($id_communication, $id_invoice);
        $fk_client = $communication->fk_client;
        if ($request->input('type_install') == 1 && $communication->type_communcation == 'تركيب' && !$updated) {
            $this->handleInstallation($communication, $id_invoice, $type, $updated, $fk_client, $data);
        }

        if ($type && !$updated) {
            $this->handlePeriodCommunication($communication);
        }

        return $this->sendSucssas($data);
    }

    private function handleInstallation(client_communication $communication,  $data, $id_invoice, $type, $updated, $fk_client)
    {
        $fk_regoin = $communication->client->fk_regoin;
        $fk_country = Regoin::where('id_regoin', $fk_regoin)->first()->fk_country;
        $valueConfig = $this->getConfigValue($fk_country, 'install_second');

        $date_last_com_install = Carbon::parse($communication->date_communication);
        $date_next = $date_last_com_install->addDays($valueConfig)->format('Y-m-d');

        $communication1 = new client_communication();
        $communication1->fk_client = $fk_client;
        $communication1->date_next = $date_next;
        $communication1->type_communcation = 'تركيب';
        $communication1->id_invoice = $id_invoice;
        $communication1->type_install = 2;
        $communication1->date_last_com_install = $date_last_com_install;
        $communication1->save();

        $this->TaskService->closeTaskAfterInstallClient($data);
        $this->updateFkUserCommunication($communication->fk_client, $date_last_com_install, $fk_country);
    }

    private function handlePeriodCommunication(client_communication $communication)
    {
        $fk_country = $communication->client->fk_regoin;
        $valueConfig = $this->getConfigValue($fk_country, 'period_commincation3');

        $result = client_communication::where('fk_client', $communication->fk_client)
            ->whereNotNull('date_next')
            ->whereNull('date_communication')
            ->where('type_communcation', 'دوري')
            ->get();

        if ($result->isEmpty()) {
            $date_next = date('Y-m-d', strtotime($communication->date_communication . ' + ' . $valueConfig . ' days'));
            $this->addCommunicationFprUpdate($communication->fk_client, $date_next);
        }
    }

    private function getConfigValue($fk_country, $name_config)
    {
        $config = config_table::where('fk_country', $fk_country)
            ->where('name_config', $name_config)
            ->first();

        return $config ? $config->value_config : null;
    }


    private function updateFkUserCommunication($fk_client, $date_comm, $fk_country)
    {
        $valueconfig = config_table::where('fk_country', $fk_country)
            ->where('name_config', 'period_commincation3')
            ->value('value_config');

        if ($valueconfig) {
            $existingCommunication = client_communication::where('fk_client', $fk_client)
                ->whereNotNull('date_next')
                ->whereNull('date_communication')
                ->where('type_communcation', 'دوري')
                ->exists();

            if (!$existingCommunication) {
                $date_communication = $date_comm;
                $date_next = date('Y-m-d', strtotime($date_communication . $valueconfig . ' days'));

                $this->addCommunicationFprUpdate($fk_client, $date_next);
            }
        }
    }

    private function addCommunicationFprUpdate($fk_client, $date_next)
    {
        client_communication::create([
            'fk_client' => $fk_client,
            'date_next' => $date_next,
            'type_communcation' => 'دوري',
        ]);
    }

    private function getCommunicationById($idCommunication)
    {
        $communication = client_communication::with(['client', 'user', 'invoice'])
            ->where('id_communication', $idCommunication)
            ->first();

        if ($communication) {
            $data = $communication->toArray();

            $data['name_enterprise'] = $communication->client->name_enterprise;
            $data['nameUser'] = $communication->user ? $communication->user->nameUser : null;
            $data['date_create'] = $communication->invoice ? $communication->invoice->date_create : null;
            $data['date_approve'] = $communication->invoice ? $communication->invoice->date_approve : null;
            $data['dateinstall_done'] = $communication->invoice ? $communication->invoice->dateinstall_done : null;
            $data['mobile'] = $communication->client->mobile;
            $data['fk_regoin'] = $communication->client->fk_regoin;
            $data['name_regoin'] = $communication->client->regoin->name_regoin;
            $data['name_client'] = $communication->client->name_client;

            return $data;
        } else {
            return [];
        }
    }
}
