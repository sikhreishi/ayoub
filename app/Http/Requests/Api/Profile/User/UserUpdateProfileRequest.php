<?php

namespace App\Http\Requests\Api\Profile\User;

use App\Http\Requests\Api\ApiFormRequest;

class UserUpdateProfileRequest extends ApiFormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $userId = $this->user()->id ?? null;
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $userId,
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $userId,
            'language' => 'sometimes|string|max:10',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gender' => 'sometimes|in:male,female,other',
        ];
    }
}
