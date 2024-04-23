<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivilegeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_privg_user' => (string)$this->id_privg_user,
            'fk_level' => (string)$this->fk_level,
            'fk_privileg' => (string)$this->fk_privileg,
            'is_check' => (string)$this->is_check,
            'name_privilege' => (string)$this->name_privilege,
            'type_prv' => (string)$this->type_prv,
            'periorty' => (string)$this->periorty,
        ];
    }
}
