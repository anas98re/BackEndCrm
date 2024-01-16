<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreclientsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $clientId = $this->route('client') ?? null; // Get the client ID if it exists

        return [
            'name_client' => 'required|unique:clients,name_client' ,
            'phone' => 'required|unique:clients,phone',
            'fk_user' => 'required|numeric',
            // Add other rules as needed
        ];
    }
}
