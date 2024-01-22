<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'public_Type' => $this->public_Type,
            'main_type_task' => $this->main_type_task,
            'recive_date' => $this->recive_date,
            'start_date' => $this->start_date,
            'deadline' => $this->deadline,
            'actual_delivery_date' => $this->actual_delivery_date,
            'hours' => $this->hours,
            'completion_percentage' => $this->completion_percentage,
            'recurring' => $this->recurring,
            'recurring_type' => $this->recurring_type,
            'Number_Of_Recurring' => $this->Number_Of_Recurring,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'dateTimeCreated' => $this->dateTimeCreated,
            'changed_date' => $this->changed_date,
            'task_statuse_id' => $this->task_statuse_id,
            'changed_by' => $this->changed_by,
            'type' => $this->type,
            'priority' => $this->priority,
            'name' => $this->name,
            'assigned_by_name' => $this->assignedByUser->nameUser ?? '',
            'assigend_department_from_name' => $this->managementsFrom->name_mange ?? '',
            'assigend_department_to_name' => $this->managements->name_mange ?? '',
            'assigend_region_from' => $this->regionsFrom->name_regoin ?? '',
            'assigend_region_to' => $this->regionsTo->name_regoin ?? '',
            'created_by' => $this->createByUser->nameUser ?? '',
            'name_regoin' => $this->regions->name_regoin ?? '',
            'task_statuses' => $this->taskStatuses,
            // 'task_statuses' => [
            //     'task_statuse_id' => $this->task_statuses->task_statuse_id ?? '',
            // ],
            'assignedByUser' => [
                'id_user' => $this->assignedByUser->id_user ?? '',
                'nameUser' => $this->assignedByUser->nameUser ?? '',
                'img_image' => $this->assignedByUser->img_image ?? '',
                'fk_regoin' => $this->assignedByUser->fk_regoin ?? '',
                'type_administration' => $this->assignedByUser->type_administration ?? '',
                'name_regoin' => $this->assignedByUser->name_regoin ?? '',
                'name_mange' => $this->assignedByUser->name_mange ?? '',
            ],
            'assignedToUser' => [
                'id_user' => $this->assignedToUser->id_user ?? '',
                'nameUser' => $this->assignedToUser->nameUser ?? '',
                'img_image' => $this->assignedToUser->img_image ?? '',
                'fk_regoin' => $this->assignedToUser->fk_regoin ?? '',
                'type_administration' => $this->assignedToUser->type_administration ?? '',
                'name_regoin' => $this->assignedToUser->name_regoin ?? '',
                'name_mange' => $this->assignedToUser->name_mange ?? '',
            ],
            'taskGroup' => [
                'id' => $this->taskGroup->id ?? '',
                'groupName' => $this->taskGroup->groupName ?? '',
            ],
            'Clients' => [
                'id_clients' => $this->Clients->id_clients ?? '',
                'name_enterprise' => $this->Clients->name_enterprise ?? '',
                'ismarketing' => $this->Clients->ismarketing ?? '',
            ],
            'invoices' => [
                'id_invoice' => $this->invoices->id_invoice ?? '',
                'stateclient' => $this->invoices->stateclient ?? '',
            ]
        ];
    }
}
