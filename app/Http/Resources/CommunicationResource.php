<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_communication' => (string)$this->id_communication,
            'fk_client' => (string)$this->fk_client,
            'fk_user' => (string)$this->fk_user,
            'date_communication' => Carbon::parse($this->date_communication)->format('Y-m-d H:i:s'),
            'result' => $this->result,
            'notes' => $this->notes,
            'rate' => $this->rate,
            'type_communcation' => $this->type_communcation,
            'number_wrong' => $this->number_wrong,
            'client_repeat' => $this->client_repeat,
            'date_next' => $this->date_next,
            'id_invoice' => (string)$this->id_invoice,
            'IDcustomer' => $this->IDcustomer,
            'user_do' => $this->user_do,
            'name_enterprisecom' => $this->name_enterprisecom,
            'address' => $this->address,
            'type_install' => (string)$this->type_install,
            'date_last_com_install' => $this->date_last_com_install,
            'client_out' => $this->client_out,
            'school' => $this->school,
            'is_suspend' => $this->is_suspend,
            'isRecommendation' => $this->isRecommendation,
            'is_visit' => $this->is_visit,
            'user_update' => (string)$this->user_update,
            'name_enterprise' => $this->client?->name_enterprise,
            'nameUser' => $this->user?->nameUser,
            'date_create' => $this->invoice?->date_create,
            'date_approve' => $this->invoice?->date_approve,
            'dateinstall_done' => $this->invoice?->dateinstall_done,
            'mobile' => $this->client?->mobile,
            'fk_regoin' => $this->client?->fk_regoin,
            'name_regoin' => $this->client?->name_regoin,
            'name_client' => $this->client?->name_client,
            'client' => [
                'id_clients' => (string)$this->client?->id_clients,
                'name_client' => (string)$this->client?->name_client,
                'name_enterprise' => (string)$this->client?->name_enterprise,
                'type_job' => (string)$this->client?->type_job,
                'city' => (string)$this->client?->city,
                'location' => (string)$this->client?->location,
                'fk_regoin' => (string)$this->client?->fk_regoin,
                'date_create' => (string)$this->client?->date_create,
                'type_client' => (string)$this->client?->type_client,
                'fk_user' => (string)$this->client?->fk_user,
                'date_transfer' => (string)$this->client?->date_transfer,
                'fkusertrasfer' => (string)$this->client?->fkusertrasfer,
                'mobile' => (string)$this->client?->mobile,
                'date_changetype' => (string)$this->client?->date_changetype,
                'reason_change' => (string)$this->client?->reason_change,
                'reason_transfer' => (string)$this->client?->reason_transfer,
                'offer_price' => (string)$this->client?->offer_price,
                'date_price' => (string)$this->client?->date_price,
                'date_price2' => (string)$this->client?->date_price2,
                'user_do' => (string)$this->client?->user_do,
                'ismarketing' => (string)$this->client?->ismarketing,
                'address_client' => (string)$this->client?->address_client,
                'date_recive' => (string)$this->client?->date_recive,
                'userAdd_email' => (string)$this->client?->userAdd_email,
                'phone' => (string)$this->client?->phone,
                'IDcustomer' => (string)$this->client?->IDcustomer,
                'descActivController' => (string)$this->client?->descActivController,
                'presystem' => (string)$this->client?->presystem,
                'sourcclient' => (string)$this->client?->sourcclient,
                'activity_type_fk' => (string)$this->client?->activity_type_fk,
                'user_add' => (string)$this->client?->user_add,
                'date_visit_Client' => (string)$this->client?->date_visit_Client,
                'done_transfer' => (string)$this->client?->done_transfer,
                'done_visit' => (string)$this->client?->done_visit,
                'tag' => (string)$this->client?->tag,
                'size_activity' => (string)$this->client?->size_activity,
                'fk_client_source' => (string)$this->client?->fk_client_source,
                'email' => (string)$this->client?->email,
                'fk_rejectClient' => (string)$this->client?->fk_rejectClient,
                'SerialNumber' => (string)$this->client?->SerialNumber,
                'is_comments_check' => (string)$this->client?->is_comments_check,
                'type_record' => (string)$this->client?->type_record,
                'reason_class' => (string)$this->client?->reason_class,
                'type_classification' => (string)$this->client?->type_classification,
                'date_update' => (string)$this->client?->date_update,
                'fkuser_update' => (string)$this->client?->fkuser_update,
                'received_date' => (string)$this->client?->received_date,
                'approveIduser_reject' => (string)$this->client?->approveIduser_reject,
                'date_reject' => (string)$this->client?->date_reject,
                'fk_user_reject' => (string)$this->client?->fk_user_reject,
                'date_approve_reject' => (string)$this->client?->date_approve_reject,
            ],
            'user' => [
                'id_user' => (string)$this->user?->id_user,
                'nameUser' => $this->user?->nameUser,
                'email' => $this->user?->email,
                'mobile' => $this->user?->mobile,
            ],
            'invoice' => [
                'id_invoice' => (string)$this->invoice?->id_invoice,
                'date_create' => (string)$this->invoice?->date_create,
                'type_pay' => (string)$this->invoice?->type_pay,
                'renew_year' => (string)$this->invoice?->renew_year,
                'type_installation' => (string)$this->invoice?->type_installation,
                'image_record' => (string)$this->invoice?->image_record,
                'fk_idClient' => (string)$this->invoice?->fk_idClient,
                'fk_idUser' => (string)$this->invoice?->fk_idUser,
                'amount_paid' => (string)$this->invoice?->amount_paid,
                'notes' => (string)$this->invoice?->notes,
                'total' => (string)$this->invoice?->total,
                'lastuserupdate' => (string)$this->invoice?->lastuserupdate,
                'dateinstall_done' => (string)$this->invoice?->dateinstall_done,
                'isdoneinstall' => (string)$this->invoice?->isdoneinstall,
                'userinstall' => (string)$this->invoice?->userinstall,
                'dateinstall_task' => (string)$this->invoice?->dateinstall_task,
                'fkusertask' => (string)$this->invoice?->fkusertask,
                'date_lastuserupdate' => (string)$this->invoice?->date_lastuserupdate,
                'reason_date' => (string)$this->invoice?->reason_date,
                'stateclient' => (string)$this->invoice?->stateclient,
                'value_back' => (string)$this->invoice?->value_back,
                'desc_reason_back' => (string)$this->invoice?->desc_reason_back,
                'reason_back' => (string)$this->invoice?->reason_back,
                'fkuser_back' => (string)$this->invoice?->fkuser_back,
                'date_change_back' => (string)$this->invoice?->date_change_back,
                'daterepaly' => (string)$this->invoice?->daterepaly,
                'fkuserdatareplay' => (string)$this->invoice?->fkuserdatareplay,
                'iduser_approve' => (string)$this->invoice?->iduser_approve,
                'isApprove' => (string)$this->invoice?->isApprove,
                'date_approve' => (string)$this->invoice?->date_approve,
                'numbarnch' => (string)$this->invoice?->numbarnch,
                'nummostda' => (string)$this->invoice?->nummostda,
                'numusers' => (string)$this->invoice?->numusers,
                'numTax' => (string)$this->invoice?->numTax,
                'imagelogo' => (string)$this->invoice?->imagelogo,
                'clientusername' => (string)$this->invoice?->clientusername,
                'address_invoice' => (string)$this->invoice?->address_invoice,
                'emailuserinv' => (string)$this->invoice?->emailuserinv,
                'nameuserinv' => (string)$this->invoice?->nameuserinv,
                'IDcustomer' => (string)$this->invoice?->IDcustomer,
                'isdelete' => (string)$this->invoice?->isdelete,
                'date_delete' => (string)$this->invoice?->date_delete,
                'user_delete' => (string)$this->invoice?->user_delete,
                'name_enterpriseinv' => (string)$this->invoice?->name_enterpriseinv,
                'ready_install' => (string)$this->invoice?->ready_install,
                'user_ready_install' => (string)$this->invoice?->user_ready_install,
                'date_readyinstall' => (string)$this->invoice?->date_readyinstall,
                'user_not_ready_install' => (string)$this->invoice?->user_not_ready_install,
                'date_not_readyinstall' => (string)$this->invoice?->date_not_readyinstall,
                'count_delay_ready' => (string)$this->invoice?->count_delay_ready,
                'isApproveFinance' => (string)$this->invoice?->isApproveFinance,
                'iduser_FApprove' => (string)$this->invoice?->iduser_FApprove,
                'Date_FApprove' => (string)$this->invoice?->Date_FApprove,
                'renew2year' => (string)$this->invoice?->renew2year,
                'participate_fk' => (string)$this->invoice?->participate_fk,
                'rate_participate' => (string)$this->invoice?->rate_participate,
                'type_back' => (string)$this->invoice?->type_back,
                'fk_regoin_invoice' => (string)$this->invoice?->fk_regoin_invoice,
                'type_seller' => (string)$this->invoice?->type_seller,
                'fk_agent' => (string)$this->invoice?->fk_agent,
                'currency_name' => (string)$this->invoice?->currency_name,
                'renew_pluse' => (string)$this->invoice?->renew_pluse,
                'payment_idAdd' => (string)$this->invoice?->payment_idAdd,
                'payment_date' => (string)$this->invoice?->payment_date,
                'file_attach' => (string)$this->invoice?->file_attach,
                'renew_agent' => (string)$this->invoice?->renew_agent,
                'file_reject' => (string)$this->invoice?->file_reject,
                'approve_back_done' => (string)$this->invoice?->approve_back_done,
                'TypeReadyClient' => (string)$this->invoice?->TypeReadyClient,
                'notes_ready' => (string)$this->invoice?->notes_ready,
                'reason_suspend' => (string)$this->invoice?->reason_suspend,
                'reason_notReady' => (string)$this->invoice?->reason_notReady,
                'date_back_now' => (string)$this->invoice?->date_back_now,
                'invoice_source' => (string)$this->invoice?->invoice_source,
                'date_updatePayment' => (string)$this->invoice?->date_updatePayment,
            ],

        ];
    }
}
