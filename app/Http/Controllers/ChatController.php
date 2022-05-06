<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function chat_with(User $user)
    {
        // Primero recuperamos al usuario que realiza la solicitud
        // $user_a = auth()->user();
        $user_a = User::find(auth()->user()->id);

        // Usuario con el que deseamos chatear
        $user_b = $user;

        //	Vamos a recuperar la sala de chat del usuario a que tambien tenga al usuario b
        $chat = $user_a->chats()->wherehas('users', function ($q) use ($user_b) {
            // Aquí buscamos la relación con el usuario b
            $q->where('chat_user.user_id', $user_b->id);
        })->first();
        if (!$chat) {
            $chat = Chat::create([]);
            $chat->users()->sync([$user_a->id, $user_b->id]);
        }
        return redirect()->route('chat.show', $chat);
    }

    public function show(Chat $chat)
    {
        // dd(auth()->user()->id);
        // dd($chat);
        abort_unless($chat->users->contains(auth()->user()->id), 403);

        return view('chat', [
            'chat' => $chat,

        ]);
    }

    public function get_users(Chat $chat)
    {
        $users = $chat->users;
        return response()->json($users);
    }

    
    public function get_messages(Chat $chat)
    {
        $messages = $chat->messages()->with('user')->get();

        return response()->json([
            'messages' => $messages
        ]);

    }
}
