<?php

namespace App\Domains\MessageModule\Services;

use App\Domains\MessageModule\Respositories\MessageRepository;
use App\Traits\ResponseService;

class MessageService {
    public function __construct(){

    }

    public static function send($request){

        $message = MessageRepository::create($request);


        return ResponseService::success('Message sent', ['message' => $message], 200);
        
    }

}