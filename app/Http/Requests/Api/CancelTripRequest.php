<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CancelTripRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cancel_reason_id' => 'required|exists:cancel_reasons,id',
            'cancel_reason_note' => 'nullable|string|max:500',
        ];
    }
}
