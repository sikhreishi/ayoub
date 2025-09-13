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

public function messages(): array
{
    return [
        // name
        'name.string'  => 'The name must be a valid text.',
        'name.max'     => 'The name may not be greater than :max characters.',

        // email
        'email.email'  => 'Please enter a valid email address.',
        'email.max'    => 'The email may not be greater than :max characters.',
        'email.unique' => 'This email is already taken.',

        // phone
        'phone.string'  => 'The phone number must be a valid text/number string.',
        'phone.max'     => 'The phone number may not be greater than :max characters.',
        'phone.unique'  => 'This phone number is already in use.',

        // language
        'language.string' => 'The language must be a valid text code.',
        'language.max'    => 'The language code may not be greater than :max characters.',

        // avatar
        'avatar.image' => 'The avatar must be an image file.',
        'avatar.mimes' => 'The avatar must be a file of type: jpeg, png, jpg, gif, svg.',
        'avatar.max'   => 'The avatar may not be greater than :max kilobytes.',

        // gender
        'gender.in' => 'The selected gender is invalid. Allowed values: male, female, other.',
    ];
}
    
}
