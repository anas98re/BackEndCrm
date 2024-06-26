<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_invoice' => $this->id_invoice,
            'date_create' => $this->date_create,
            'type_pay' => $this->type_pay,
            'renew_year' => $this->renew_year,
            'type_installation' => $this->type_installation,
            'image_record' => $this->image_record,
            'fk_idClient' => $this->fk_idClient,
            'fk_idUser' => $this->fk_idUser,
            'amount_paid' => $this->amount_paid,
            'notes' => $this->notes,
            'total' => $this->total,
            'lastuserupdate' => $this->lastuserupdate,
            'dateinstall_done' => $this->dateinstall_done,
            'isdoneinstall' => $this->isdoneinstall,
            'userinstall' => $this->userinstall,
            'dateinstall_task' => $this->dateinstall_task,
            'fkusertask' => $this->fkusertask,
            'date_lastuserupdate' => $this->date_lastuserupdate,
            'reason_date' => $this->reason_date,
            'stateclient' => $this->stateclient,
            'value_back' => $this->value_back,
            'desc_reason_back' => $this->desc_reason_back,
            'reason_back' => $this->reason_back,
            'fkuser_back' => $this->fkuser_back,
            'date_change_back' => $this->date_change_back,
            'daterepaly' => $this->daterepaly,
            'fkuserdatareplay' => $this->fkuserdatareplay,
            'iduser_approve' => $this->iduser_approve,
            'isApprove' => $this->isApprove,
            'date_approve' => $this->date_approve,
            'numbarnch' => $this->numbarnch,
            'nummostda' => $this->nummostda,
            'numusers' => $this->numusers,
            'numTax' => $this->numTax,
            'imagelogo' => $this->imagelogo,
            'clientusername' => $this->clientusername,
            'address_invoice' => $this->address_invoice,
            'emailuserinv' => $this->emailuserinv,
            'nameuserinv' => $this->nameuserinv,
            'IDcustomer' => $this->IDcustomer,
            'isdelete' => $this->isdelete,
            'date_delete' => $this->date_delete,
            'user_delete' => $this->user_delete,
            'name_enterpriseinv' => $this->name_enterpriseinv,
            'ready_install' => $this->ready_install,
            'user_ready_install' => $this->user_ready_install,
            'date_readyinstall' => $this->date_readyinstall,
            'user_not_ready_install' => $this->user_not_ready_install,
            'date_not_readyinstall' => $this->date_not_readyinstall,
            'count_delay_ready' => $this->count_delay_ready,
            'isApproveFinance' => $this->isApproveFinance,
            'iduser_FApprove' => $this->iduser_FApprove,
            'Date_FApprove' => $this->Date_FApprove,
            'renew2year' => $this->renew2year,
            'participate_fk' => $this->participate_fk,
            'rate_participate' => $this->rate_participate,
            'type_back' => $this->type_back,
            'fk_regoin_invoice' => $this->fk_regoin_invoice,
            'type_seller' => $this->type_seller,
            'fk_agent' => $this->fk_agent,
            'currency_name' => $this->currency_name,
            'renew_pluse' => $this->renew_pluse,
            'payment_idAdd' => $this->payment_idAdd,
            'payment_date' => $this->payment_date,
            'file_attach' => $this->file_attach,
            'renew_agent' => $this->renew_agent,
            'file_reject' => $this->file_reject,
            'approve_back_done' => $this->approve_back_done,
            'TypeReadyClient' => $this->TypeReadyClient,
            'notes_ready' => $this->notes_ready,
            'reason_suspend' => $this->reason_suspend,
            'reason_notReady' => $this->reason_notReady,
            'date_back_now' => $this->date_back_now,
            'invoice_source' => $this->invoice_source,
            'date_updatePayment' => $this->date_updatePayment,
            'nameUser' => $this->user?->nameUser,
            'name_client' => $this->client?->name_client,
            'name_enterprise' => $this->client?->name_enterprise,
            'fk_regoin' => $this->client?->fk_regoin,
            'name_regoin' => $this->client?->regoin?->name_regoin,
            'name_regoin_invoice' => $this->regoin?->name_regoin,
            'type_client' => $this->client?->type_client,
            'mobile' => $this->client?->mobile,
            'ismarketing' => $this->client?->ismarketing,
            'lastuserupdateName' => $this->userUpdated?->nameUser,
            'nameuserinstall' => $this->userInstalled?->nameUser,
            'nameuserApprove' => $this->userApproved?->nameUser,
            'fk_country' => $this->client?->regoin?->fk_country,
            'nameuserback' => $this->userBack?->nameUser,
            'nameuserreplay' => $this->userReplay?->nameUser,
            'nameusertask' => $this->userTask?->nameUser,
            'city' => $this->client?->city,
            'name_city' => $this->client?->cityRelation?->name_city,
            'namemaincity' => $this->client?->cityRelation?->mainCity?->namemaincity,
            'id_maincity' => $this->client?->cityRelation?->mainCity?->id_maincity,
            'nameuser_ready_install' => $this->userReadyInstall?->nameUser,
            'nameuser_notready_install' => $this->userNotReadyInstall?->nameUser,
            'tag' => $this->client?->tag,
            'participal_info' => $this->participate ? [$this->participate] : null,
            'agent_distibutor_info' => $this->agent ? [$this->agent] : null,
            'files_attach' => $this->files,
            'products' => $this->products->map(function ($product) {
                return [
                    'id_product' => $product->id_product,
                    'nameProduct' => $product->nameProduct,
                    'priceProduct' => $product->priceProduct,
                    'type' => $product->type,
                    'fk_country' => $product->fk_country,
                    'fk_config' => $product->fk_config,
                    'idprd' => $product->idprd,
                    'created_at' => $product->created_at,
                    'fkusercreate' => $product->fkusercreate,
                    'updated_at' => $product->updated_at,
                    'fkuserupdate' => $product->fkuserupdate,
                    'type_prod_renew' => $product->type_prod_renew,
                    'fk_id_invoice' => $product->pivot->fk_id_invoice,
                    'fk_product' => $product->pivot->fk_product,
                    'id_invoice_product' => $product->pivot->id_invoice_product,
                    'amount' => $product->pivot->amount,
                    'price' => $product->pivot->price,
                    'taxtotal' => $product->pivot->taxtotal,
                    'rate_admin' => $product->pivot->rate_admin,
                    'rateUser' => $product->pivot->rateUser,
                    'idinvoice' => $product->pivot->idinvoice,
                    'name_prod' => $product->pivot->name_prod,
                ];
            }),
        ];
    }
}
