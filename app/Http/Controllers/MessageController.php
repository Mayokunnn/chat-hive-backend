<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Traits\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;

class MessageController extends Controller
{

    use Response;
    protected $storage;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env("FIREBASE_SERVICE_ACCOUNT")))
            ->withProjectId(env('FIREBASE_PROJECT_ID'))
            ->createStorage();

        $this->storage = $firebase;
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'conversation_id' => 'required|exists:conversations,id',
                'sender_id' => 'required|exists:users,id',
                'type' => 'required|in:text,image,video,file',
                'content' => 'required_if:type,text|string',
                'file' => 'required_if:type,image,video,file|file',
            ]
        );

        $data = $request->only(['conversation_id', 'sender_id', 'type', 'content']);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'uploads/' . time() . '.' . $file->getClientOriginalExtension();
            $localPath = $file->getPathname();

            $bucket = $this->storage->getBucket();
            $bucket->upload(fopen($localPath, 'r'), [
                'name' => $fileName
            ]);

            $data['url'] = $bucket->object($fileName)->signedUrl(new \DateTime('9999-12-31')); //

            $message = Message::create($data);

            return $this->success('Message sent', ['message' => $message], 201);
        }
    }
}
