@extends('layouts.app')

@section('css')
    <style>
        .right {
            display: flex;
            flex-direction: row-reverse;
        }
        .left {
            display: flex;
            flex-direction: row;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <h2> Group All User Chats</h2>
        <div class="card">
            <div class="card-header">
                   Name : {{auth()->user()->name}}
            </div>

            <div class="card-body">
                <ul class="chat">
                    @foreach($messages as $message)
                        @if(auth()->user()->id == $message->user_id)
                            <li class="right">
                        @else
                            <li class="left">
                        @endif
                            <div class="clearfix">
                                <div class="header">
                                    <strong>
                                        {{ $message->user->name }}
                                    </strong>
                                </div>
                                <p>
                                    {{ $message->message }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer">
                <div class="input-group">
                    <input
                        id="message"
                        type="text"
                        name="message"
                        class="form-control input-sm"
                        placeholder="Type your message here..."
                    />
                      <button class="btn btn-primary" id="sendMessage" >
                        Send
                      </button>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script>
    $(function (){
        const Http = window.axios;
        const Echo = window.Echo;
        const id = {{auth()->user()->id}};
        const name = `{{auth()->user()->name}}`;
        const message = $("#message");

        $("#sendMessage").click(function(){
            if(message.val() == ''){
                message.addClass('is-invalid');
            }else{
                Http.post("{{url('messages')}}",{
                    'message' : message.val()
                }).then(()=>{
                    message.val('');
                })
            }
        });
        let direction;
        let channel = Echo.channel('channel-chat');
        channel.listen('ChatEvent',function (data){
            if(id == data.message.user.id ){
                direction = "right";
            }else{
                direction = "left";
            }
            $('.chat').append(`
                   <li class="${direction}">
                    <div class="clearfix">
                        <div class="header">
                            <strong>${ data.message.user.name }</strong>
                        </div>
                    <p>${ data.message.message }</p>
                    </div>
                </li>
`)
        })
    })
</script>
@endsection
