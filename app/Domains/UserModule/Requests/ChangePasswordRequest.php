<?php

namespace App\Domains\UserModule\Requests;

use Anik\Form\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8',
        ];
    }
}
