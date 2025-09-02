<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $message
 */
class ReplyTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Message is required'
        ];
    }
}
