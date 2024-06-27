<?php

namespace App\Http\Controllers;

use App\Domains\MessageModule\Models\Message;
use App\Domains\MessageModule\Requests\SendMessageRequest;
use App\Domains\MessageModule\Requests\UpdateMessageRequest;
use App\Domains\MessageModule\Services\MessageService;
use App\Traits\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;

class MessageController extends Controller
{

  public function getAll($conversation_id)
  {
    return MessageService::all($conversation_id);
  }

  public function send(SendMessageRequest $request, $conversation_id)
  {
    return MessageService::send($request, $conversation_id);
  }

  public function edit(UpdateMessageRequest $request, $message_id, $conversation_id)
  {
    return MessageService::edit($request, $message_id, $conversation_id);
  }

  public function delete($message_id, $conversation_id)
  {
    return MessageService::delete($message_id, $conversation_id);
  }
}
