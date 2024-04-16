<?php

namespace App\Http\Controllers;

use App\Models\client_communication;
use App\Http\Requests\Storeclient_communicationRequest;
use App\Http\Requests\Updateclient_communicationRequest;
use App\Models\clientCommentMention;
use Illuminate\Support\Facades\DB;
use App\Models\ClientCommunication;
use App\Models\Clients;
use App\Models\Regoin;
use App\Models\Users;
use App\Models\ClientInvoice;
use App\Models\config_table;
use Illuminate\Http\Request;

class ClientCommunicationController extends Controller
{
    public function updateCommunication(Request $request)
    {
        $id_communication = request()->query('id_communication');

        $communication = client_communication::with(['client', 'user', 'invoice'])
            ->where('id_communication', $id_communication)
            ->first();
        $id_invoice = $request->input('id_invoice');
        if ($communication) {
            $communication->date_communication = $request->input('date_communication', $communication->date_communication);
            $communication->type_communication = $request->input('type_communication', $communication->type_communication);
            $communication->fk_user = $request->input('fk_user', $communication->fk_user);
            $communication->result = $request->input('result', $communication->result);
            $communication->rate = $request->input('rate', $communication->rate);
            $communication->number_wrong = $request->input('number_wrong', $communication->number_wrong);
            $communication->client_repeat = $request->input('client_repeat', $communication->client_repeat);
            $communication->is_suspend = $request->input('is_suspend', $communication->is_suspend);
            $communication->isRecommendation = $request->input('isRecommendation', $communication->isRecommendation);
            $communication->is_visit = $request->input('is_visit', $communication->is_visit);

            if ($request->input('updated') == null) {
                $communication->date_next = $request->input('date_next', $communication->date_next);
            }

            $communication->save();
        }

        $type = $request->input('type');
        $updated = $request->input('updated');
        $data['communication'] = $communication;
        $data = $this->getCommunicationById($id_communication, $id_invoice);

        if ($request->input('type_install') == 1 && $communication->type_communication == 'تركيب' && !$updated) {
            $this->handleInstallation($communication, $id_invoice, $type, $updated);
        }

        if ($type && !$updated) {
            $this->handlePeriodCommunication($communication);
        }

        return $this->sendSucssas($data);
    }

    private function handleInstallation(client_communication $communication, $id_invoice, $type, $updated)
    {
        $fk_country = $communication->client->fk_regoin;
        $valueConfig = $this->getConfigValue($fk_country, 'install_second');

        $date_last_com_install = $communication->date_communication;
        $date_next = date('Y-m-d', strtotime($communication->date_communication . ' + ' . $valueConfig . ' days'));

        $communication->client->communications()->create([
            'date_next' => $date_next,
            'type_communication' => 'تركيب',
            'id_invoice' => $id_invoice,
            'type_install' => 2,
            'date_last_com_install' => $date_last_com_install,
        ]);
        if ($type && !$updated) {
            $this->updateFkUserCommunication($communication->fk_client, $date_last_com_install, $fk_country);
        }
    }

    private function handlePeriodCommunication(client_communication $communication)
    {
        $fk_country = $communication->client->fk_regoin;
        $valueConfig = $this->getConfigValue($fk_country, 'period_commincation3');

        $result = client_communication::where('fk_client', $communication->fk_client)
            ->whereNotNull('date_next')
            ->whereNull('date_communication')
            ->where('type_communication', 'دوري')
            ->get();

        if ($result->isEmpty()) {
            $date_next = date('Y-m-d', strtotime($communication->date_communication . ' + ' . $valueConfig . ' days'));
            $this->addCommunication($communication->fk_client, $date_next);
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

                    $this->addCommunication($fk_client, $date_next);
                }
            }
    }

    private function addCommunication($fk_client, $date_next)
    {
        client_communication::create([
            'fk_client' => $fk_client,
            'date_next' => $date_next,
            'type_communication' => 'دوري',
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
