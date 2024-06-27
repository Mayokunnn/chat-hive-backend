<?php

namespace App\Domains\ConversationModule\Requests;

use Anik\Form\FormRequest;

class AddConversationUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'userId' => 'required|exists:users,id|string'
        ];
    }
}
