<?php

namespace App\Http\Controllers;

use App\Events\MessageSend;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function send(Request $request)
    {

        $user = User::find(auth()->user()->id);

        $message = $user->messages()->create([
            'user_id' => auth()->user()->id,
            'content' => $request->message,
            'chat_id' => $request->chat_id
        ])->load('user');

        // $message = auth()->user()->messages()->create([
        //     'content' => $request->message,
        //     'chat_id' => $request->chat_id
        // ])->load('user');

        //DISPARAMOS EL EVENTO A LOS CANALES DEL USUARIO
        broadcast(new MessageSend($message))->toOthers();


        return $message;
    }
}
