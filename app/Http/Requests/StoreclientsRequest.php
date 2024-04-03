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
            'name_client' => 'required|string' ,
            'phone' => 'nullable|numeric',
            'fk_user' => 'required|numeric',
            'name_enterprise' => 'nullable|string',
            'address_client' => 'nullable|string',
            'type_job' => 'nullable|string',
            'city' => 'nullable|numeric',
            'location' => 'nullable|string',
            'date_create' => 'nullable|date_format:Y-m-d',
            'mobile' => 'nullable|numeric',
            'email' => 'nullable|email',
            'size_activity' => 'nullable|string',
            'date_transfer' => 'nullable|date_format:Y-m-d',
            'descActivController' => 'nullable|string',
            'fk_regoin' => 'nullable|numeric',
            'type_client' => 'nullable|string',
            'user_do' => 'nullable|numeric',
            'courcclient' => 'nullable|string',
            'ismarketing' => 'nullable|numeric',
            // Add other rules as needed
        ];
    }
}
