<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientTransferedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_clients' => $this->id_clients,
            'name_client' => $this->name_client,
            'name_enterprise' => $this->name_enterprise,
            'type_job' => $this->type_job,
            'city' => $this->city,
            'location' => $this->location,
            'fk_regoin' => $this->fk_regoin,
            'date_create' => $this->date_create,
            'received_date' => $this->received_date,
            'type_client' => $this->type_client,
            'fk_user' => $this->fk_user,
            'date_transfer' => $this->date_transfer,
            'fkusertrasfer' => $this->fkusertrasfer,
            'mobile' => $this->mobile,
            'date_changetype' => $this->date_changetype,
            'reason_change' => $this->reason_change,
            'reason_transfer' => $this->reason_transfer,
            'offer_price' => $this->offer_price,
            'date_price' => $this->date_price,
            'date_price2' => $this->date_price2,
            'user_do' => $this->user_do,
            'ismarketing' => $this->ismarketing,
            'address_client' => $this->address_client,
            'date_recive' => $this->date_recive,
            'userAdd_email' => $this->userAdd_email,
            'phone' => $this->phone,
            'IDcustomer' => $this->IDcustomer,
            'descActivController' => $this->descActivController,
            'presystem' => $this->presystem,
            'sourcclient' => $this->sourcclient,
            // other
            'activity_type_fk' => $this->activity_type_fk,
            'user_add' => $this->user_add,
            'date_visit_Client' => $this->date_visit_Client,
            'done_transfer' => $this->done_transfer,
            'done_visit' => $this->done_visit,
            'tag' => $this->tag,
            'size_activity' => $this->size_activity,
            'fk_client_source' => $this->fk_client_source,
            'email' => $this->email,
            'fk_rejectClient' => $this->fk_rejectClient,
            'SerialNumber' => $this->SerialNumber,
            'is_comments_check' => $this->is_comments_check,
            'type_record' => $this->type_record,
            'reason_class' => $this->reason_class,
            'type_classification' => $this->type_classification,
            'date_update' => $this->date_update,
            'fkuser_update' => $this->fkuser_update,
            'approveIduser_reject' => $this->approveIduser_reject,
            'date_reject' => $this->date_reject,
            'date_approve_reject' => $this->date_approve_reject,
            'fk_user_reject' => $this->fk_user_reject,
            'nameCountry' => $this->regoin?->country?->nameCountry,
            'name_regoin' => $this->regoin?->name_regoin,
            'nameUser' => $this->user?->nameUser,
            'mobileUser' => $this->user?->mobile,
            'nameuserdoning' => $this->userDo?->nameUser,
            'fk_country' => $this->regoin?->fk_country,
            'nameusertransfer' => $this->userTransfer?->nameUser,
            'name_city' => $this->cityRelation?->name_city,
            'namemaincity' => $this->cityRelation?->mainCity?->namemaincity,
            'id_maincity' => $this->cityRelation?->mainCity?->id_maincity,
            'activity_type_title' => $this->activityType?->name_activity_type,
            'presystemtitle' => $this->preSystemRelation?->name_company,
            'nameAdduser' => $this->userAdd?->nameUser,
            'NameReason_reject' => $this->reasonReject?->NameReason_reject,
            'nameTransferTo' => $this->transferTo?->nameUser,
        ];
    }
}
