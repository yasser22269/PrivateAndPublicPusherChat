<?php

namespace App\Http\Controllers;

use App\Events\PrivateChatEvent;
use App\Models\ChMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrivateChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $users = User::get();
        $messages =  ChMessage::with('user_to','user_from')
            ->where('from_id','=',auth()->user()->id)
            ->where('to_id','=',auth()->user()->id)
            ->get();

        return view('PrivateChat.chat',compact('messages','users'));


    }

    public function fetchMessages($id)
    {
        $auth= auth()->user()->id;
        if($auth ==$id){
            return ChMessage::
            whereColumn('from_id','=','to_id')->
            where('from_id','=',$id)->with('user_to','user_from')->get();
        }
        return ChMessage::
        where(function ($query) use ($id,$auth) {
            $query->where('from_id', '=', $id)->Where('to_id', '=', $auth);
        })->orwhere(function ($query) use ($id,$auth) {
            $query->where('to_id', '=', $id)->Where('from_id', '=', $auth);
        })->with('user_to','user_from')->get();

    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        $message = CHMessage::create([
            'body' => $request->input('body'),
            'from_id' => $request->input('from_id'),
            'to_id' => $request->input('to_id'),
        ]);
        $user_from = $message->user_from;
        $user_to = $message->user_to;
        PrivateChatEvent::dispatch($message,$user_from,$user_to);
        return [$message,$user_from,$user_to];
    }
}
