<?php

namespace App\Domains\MessageModule\Requests;

use Anik\Form\FormRequest;
use App\Domains\MessageModule\Rules\MessageBelongsToConversation;

class UpdateMessageRequest extends FormRequest
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
        $messageId = $this->input('messageId');
        $conversationId = $this->input('conversationId');

        return [
            'messageId' => 'required|exists:messages,id',
            'conversationId' => [
                'required',
                'exists:conversations,id',
                new MessageBelongsToConversation($messageId, $conversationId)
            ],
            'type' => 'required|in:text,image,video,file',
            'content' => 'required_if:type,text|string',
            'file' => 'required_if:type,image,video,file|file',
        ];
    }
}
