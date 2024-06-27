<?php

namespace App\Domains\MessageModule\Requests;

use Anik\Form\FormRequest;

class SendMessageRequest extends FormRequest
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
            'conversationId' => 'required|exists:conversations,id',
            'senderId' => 'required|exists:users,id',
            'type' => 'required|in:text,image,video,file',
            'content' => 'required_if:type,text|string',
            'file' => 'required_if:type,image,video,file|file',
        ];
    }
}
