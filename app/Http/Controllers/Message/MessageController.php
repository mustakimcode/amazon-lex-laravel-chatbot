<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Models\Messages;
use Illuminate\Http\Request;

use Aws\LexRuntimeService\LexRuntimeServiceClient;
use Illuminate\Support\Facades\Auth;
use DB;

class MessageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $botConfig;

    public function __construct()
    {
        $this->middleware('auth');
        $this->botConfig = [
            'region' => env("AWS_DEFAULT_REGION"),
            'version' => 'latest',
            'botName' => 'KimBot',
            'botAlias' => 'KimBotBasic',
        ];
    }

    public function send(Request $request)
    {
        $botConfig = [
            'region' => env("AWS_DEFAULT_REGION"),
            'version' => 'latest',
            'botName' => 'KimBot',
            'botAlias' => 'KimBotBasic',
        ];

        $lexRuntimeServiceClient = new LexRuntimeServiceClient($this->botConfig);

        $newMessage = $botConfig;
        $newMessage['inputText'] = $request->message;
        $newMessage['userId'] = Auth::user()->id;

        $returnMessage = $lexRuntimeServiceClient->postText($newMessage);

        $newMessage = [
            [
                'message' => $request->message,
                'sender_id' => Auth::user()->id,
                'sender_name' => Auth::user()->name,
                "session_id" => $returnMessage['sessionId']
            ],
        ];

        if ($returnMessage["dialogState"] != "ReadyForFulfillment") {
            $newMessage[] =
                [
                    'message' => $returnMessage["message"],
                    'sender_name' => "Lex Luthor",
                    'sender_id' => "Lex Luthor",
                    "session_id" => $returnMessage['sessionId']
                ];
        }

        $messsages = [];
        foreach ($newMessage as $key => $value) {
            $messsages[] = Messages::create([
                "session_id" => $returnMessage["sessionId"],
                'message' => $value["message"],
                'sender_id' => $value["sender_id"],
                'sender_name' =>  $value["sender_name"],
            ]);
        }

        return [
            "dialogState" => $returnMessage["dialogState"],
            "slots" => $returnMessage["slots"],
            "messages" => $messsages
        ];
    }

    public function init()
    {
        $lexRuntimeServiceClient = new LexRuntimeServiceClient($this->botConfig);

        $this->botConfig['userId'] = Auth::user()->id;
        $newSessionData = $lexRuntimeServiceClient->putSession($this->botConfig);

        return [
            "dialogState" => $newSessionData["dialogState"],
            "messages" => [Messages::create([
                "session_id" => $newSessionData["sessionId"],
                'message' => "Hallo!",
                'sender_name' => "Lex Luthor",
                'sender_id' => "Lex Luthor",
            ])],
            "slots" => $newSessionData["slots"],
        ];
    }
}
