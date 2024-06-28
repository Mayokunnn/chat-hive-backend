<?php

namespace App\Domains\MessageModule\Services;

use App\Domains\ConversationModule\Repositories\ConversationRepository;
use App\Domains\MessageModule\Resources\MessageResource;
use App\Domains\MessageModule\Repositories\MessageRepository;
use App\Traits\ResponseService;
use Exception;

class MessageService
{
    public static function all($conversation_id)
    {
        try {
            $conversation = ConversationRepository::getConversationById($conversation_id);

            if (empty($conversation)) {
                return ResponseService::error('Request Error: Conversation not found', [], 400);
            }

            $messages = MessageRepository::getMessagesInConversation($conversation_id);

            if (count($messages) == 0) {
                return ResponseService::success('No message yet', [], 200);
            }

            return ResponseService::success('Success', [MessageResource::collection($messages)], 200);
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to retrieve messages', [], 500);
        }
    }

    public static function send($request, $conversationId)
    {
        try {
            $conversation = ConversationRepository::getConversationById($conversationId);

            if (empty($conversation)) {
                return ResponseService::error('Request Error: Conversation not found', [], 400);
            }

            $message = MessageRepository::create($request);

            return ResponseService::success('Message sent', [new MessageResource($message)], 200);
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to send message', [], 500);
        }
    }

    public static function edit($request, $messageId, $conversationId)
    {
        try {
            $conversation = ConversationRepository::getConversationById($conversationId);

            if (empty($conversation)) {
                return ResponseService::error('Request Error: Conversation not found', [], 400);
            }

            $message = MessageRepository::getMessageById($messageId);

            if (empty($message)) {
                return ResponseService::error('Request Error: Message not found', [], 400);
            }

            if ($message->conversation_id != $conversationId) {
                return ResponseService::error('Request Error: Message does not belong to the specified conversation', [], 400);
            }

            if ($message->sender_id !== auth()->user()->id) {
                return ResponseService::error('Request Error: You are not the sender of this message', [auth()->user()], 400);
            }

            $message = MessageRepository::updateMessage($messageId, $request);

            return ResponseService::success('Message sent', [new MessageResource($message)], 200);
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to edit message', [], 500);
        }
    }

    public static function delete($message_id, $conversation_id)
    {
        try {
            $conversation = ConversationRepository::getConversationById($conversation_id);

            if (empty($conversation)) {
                return ResponseService::error('Request Error: Conversation not found', [], 400);
            }

            $message = MessageRepository::getMessageById($message_id);

            if (empty($message)) {
                return ResponseService::error('Request Error: Message not found', [], 400);
            }

            if ($message->conversation_id != $conversation_id) {
                return ResponseService::error('Request Error: Message does not belong to the specified conversation', [], 400);
            }

            if ($message->sender_id !== auth()->user()->id) {
                return ResponseService::error('Request Error: You are not the sender of this message', [], 400);
            }

            // Proceed with deleting the message
            $deleted = MessageRepository::deleteMessage($message_id);

            // Check if the delete operation was successful
            if ($deleted) {
                return ResponseService::success('Message deleted successfully', [], 200);
            } else {
                return ResponseService::error('Server Error: Failed to delete the message', [], 500);
            }
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to delete message', [], 500);
        }
    }
}

