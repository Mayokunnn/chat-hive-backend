<?php

namespace App\Domains\ConversationModule\Requests;

use Anik\Form\FormRequest;

class CreateConversationRequest extends FormRequest
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
            'user_ids' => 'required|array|min:2|max:2', // Ensure there are at least 2 users
            'user_ids.*' => 'exists:users,id', // Ensure each user ID exists in the users table
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }
}
