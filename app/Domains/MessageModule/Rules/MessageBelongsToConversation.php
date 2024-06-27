<?php

namespace App\Domains\MessageModule\Rules;

use App\Domains\MessageModule\Respositories\MessageRepository;
use Illuminate\Contracts\Validation\Rule;

class MessageBelongsToConversation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $messageId;
    protected $conversationId;

    public function __construct($messageId, $conversationId)
    {
        $this->messageId = $messageId;
        $this->conversationId = $conversationId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $message = MessageRepository::getMessageById($this->messageId);
        return $message && $message->conversation_id == $this->conversationId;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The message does not belong to the specified conversation.';
    }
}
