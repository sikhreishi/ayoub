<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class ResendOTPRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => 'Phone number is required.',
        ];
    }
}
