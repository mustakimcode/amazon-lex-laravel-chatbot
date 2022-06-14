<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Aws\LexRuntimeService\LexRuntimeServiceClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use DB;

class MessageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function send(Request $request)
    {
        $body = [
            'region' => 'ap-southeast-1',
            'version' => 'latest',
            'botName' => 'KimBot',
            'botAlias' => 'KimBotBasic',
            'inputText' => $request->message,
            'userId' => Auth::user()->id,
        ];


        $newSess = new LexRuntimeServiceClient($body);
        $returnMessage = $newSess->postText($body);
        // Log::error($returnMessage);

        $newMessage = [
            [
                'message' => $request->message,
                'sender_id' => Auth::user()->id,
                "session_id" => $returnMessage['sessionId']
            ],
        ];
        if ($returnMessage["dialogState"] != "ReadyForFulfillment") {
            $newMessage[] =
                [
                    'message' => $returnMessage["message"],
                    'sender_name' => "Lex Luthor",
                    // 'sender_id' => "Lex Luthor",
                    "session_id" => $returnMessage['sessionId']
                ];
        }
        DB::table("messages")->insert($newMessage);

        return [
            "dialogState" => $returnMessage["dialogState"],
            "slots"=> $returnMessage["slots"],
            "messages" => $newMessage
        ];
    }
}
