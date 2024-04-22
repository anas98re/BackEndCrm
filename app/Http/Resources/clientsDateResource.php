<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class clientsDateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'fk_user' => (string) $this->fk_user,
            'fk_userName' => (string) $this->user?->nameUser,
            'date_client_visit' => (string)  $this->date_client_visit,
            'is_done' => (string) $this->is_done,
            'fk_invoice' => (string) $this->fk_invoice,
            'type_date' => (string) $this->date_comment,
            'fk_agent' => (string)  $this->fk_agent,
            'agentName' => (string) $this->agent?->name_agent,
            'date_end' => (string)  $this->date_end,
            'fk_user_add' => (string) $this->fk_user_add,
            'fk_userAddName' => (string) $this->userAdd?->nameUser,
            'fk_client' => (string)  $this?->fk_client,
            'clientName' => (string) $this->client?->name_enterprise,
            'idclients_date' => (string) $this->idclients_date,
            'fk_user_update' => (string) $this->fk_user_update,
            'user_updateName' => (string) $this->userUpdate?->nameUser,
        ];
    }
}
