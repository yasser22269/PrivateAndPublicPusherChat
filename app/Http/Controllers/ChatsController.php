<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
       // $users = User::where('id','!=',auth()->user()->id)->get();
        $messages =  Message::with('user')->get();

        return view('chat',compact('messages'));


    }

    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        $send = [
            'user' => $message->user,
            'message' => $request->input('message'),
        ];
        ChatEvent::dispatch($send);
        return $send;
    }
}
