<?php

namespace App\Domains\ConversationModule\Requests;

use Anik\Form\FormRequest;

class AddGroupParticipationRequest extends FormRequest
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
            'userIds' => 'required|array',
            'userIds.*' => 'exists:users,id', // Ensure each user ID exists in the 'users' table
        ];
    }
}
