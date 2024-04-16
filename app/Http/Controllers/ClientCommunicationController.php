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

class ClientCommunicationController extends Controller
{
    public function updateCommuncation()
    {
        $idCommunication = request()->input('id_communication');

        $communication = client_communication::with(['client', 'user', 'invoice', 'client.regoin'])
            ->where('id_communication', $idCommunication)
            ->first();

        if ($communication) {
            $communication->date_communication = request()->input('date_communication', $communication->date_communication);
            $communication->type_communication = request()->input('type_communication', $communication->type_communication);
            $communication->fk_user = request()->input('fk_user', $communication->fk_user);
            $communication->result = request()->input('result', $communication->result);
            $communication->rate = request()->input('rate', $communication->rate);
            $communication->number_wrong = request()->input('number_wrong', $communication->number_wrong);
            $communication->client_repeat = request()->input('client_repeat', $communication->client_repeat);
            $communication->is_suspend = request()->input('is_suspend', $communication->is_suspend);
            $communication->date_next = $communication->date_next;
            $communication->isRecommendation = request()->input('isRecommendation', $communication->isRecommendation);
            $communication->is_visit = request()->input('is_visit', $communication->is_visit);
            $communication->save();
        }

        $arrJson = $this->getCommunicationById($idCommunication);

        return $arrJson;
    }

    private function getcommunicationbyId($idcom)
    {
        $communication = client_communication::with(['client', 'user', 'invoice', 'client.regoin'])
            ->where('id_communication', $idcom)
            ->first();

        if ($communication) {
            return $communication->toArray();
        }

        return [];
    }
}
