<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class companrCommentResources extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_comment_company' => (string)$this->id_comment_company,
            'fk_user' => (string)$this->fk_user,
            'fk_company' => (string)$this->fk_company,
            'content' => $this->content,
            'date_comment' => $this->date_comment,
            'nameUser' => $this->Users->nameUser,
            'img_image' => $this->Users->img_image,
        ];
    }
}
