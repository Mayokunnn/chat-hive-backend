<?php

namespace App\Http\Controllers;

use App\Domains\MessageModule\Models\Message;
use App\Domains\MessageModule\Requests\SendMessageRequest;
use App\Domains\MessageModule\Services\MessageService;
use App\Traits\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;

class MessageController extends Controller
{

    public function send(SendMessageRequest $request)
    {
      return MessageService::send($request);
    }
}
