<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_user' => (string) $this->id_user,
            'nameUser' => $this->nameUser,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'code_verfiy' => (string)$this->code_verfiy,
            'fk_country' => (string)$this->fk_country,
            'type_administration' => (string)$this->type_administration,
            'type_level' => (string)$this->type_level,
            'fk_regoin' => (string)$this->fk_regoin,
            'img_image' => $this->img_image,
            'img_thumbnail' => $this->img_thumbnail,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'fkuserAdd' => (string)$this->fkuserAdd,
            'fkuserupdate' => (string)$this->fkuserupdate,
            'isActive' => (string)$this->isActive,
            'salary' => (string) $this->salary,
            'email_pluse' => (string) $this->email_pluse,
            'maincity_fk' => (string)$this->maincity_fk,
            'nameCountry' => $this->country?->nameCountry,
            'name_regoin' => $this->regions?->name_regoin,
            'name_level' => $this->level?->name_level,
            'periorty' => $this->level?->pivot?->periorty,
            'currency' => $this->country?->currency,
            'nameuserAdd' => (string)$this->fkuserAdd,
            'name_mange' => $this->managements?->name_mange,
            'nameuserupdate' => $this->fkuserupdate,
            'privilegelist' => PrivilegeResource::collection($this->privileges),
            'maincitylist_user' => MainCityResource::collection($this->mainCity),
        ];
    }
}
