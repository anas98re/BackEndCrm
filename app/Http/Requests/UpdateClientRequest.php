<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_client' => 'nullable|string' ,
            'phone' => 'nullable|numeric',
            'fk_user' => 'nullable|numeric',
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
        ];
    }
}
