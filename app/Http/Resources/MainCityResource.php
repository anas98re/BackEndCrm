<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainCityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'iduser_maincity' => (string) $this->iduser_maincity,
            'fk_maincity' => (string) $this->fk_maincity,
            'fk_user' => (string) $this->fk_user,
            'namemaincity' => (string) $this->namemaincity,
            'fk_country' => (string) $this->fk_country,
        ];
    }
}
