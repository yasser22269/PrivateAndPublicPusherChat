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
        li.right .header{
            display: flex;
            justify-content: flex-end;
        }
        li.left .header{
            display: flex;
            justify-content: flex-start;
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
                        @if(
                                auth()->user()->id == $message->from_id
                            )
                            <li class="right">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>
                                            {{ $message->user_from->name }}
                                        </strong>
                                    </div>
                                    <p>
                                        {{ $message->body }}
                                    </p>
                                </div>
                            </li>
                        @else
                            <li class="left">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>
                                            {{ $message->user_to->name }}
                                        </strong>
                                    </div>
                                    <p>
                                        {{ $message->body }}
                                    </p>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="card-footer">
                <div class="input-group">
                    <select name="user_id" id="getuserselect"  class="form-select">
                        @foreach($users as $user)
                            <option  value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                    </select>
                </div>
                <br>
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
        let getuserselect = $("#getuserselect option:selected").val();
        const AllMessages = [];
        let session;
        $("#getuserselect").change(function() {
            getuserselect = $("#getuserselect option:selected").val();
            let url = "{{url("Private/messages" )}}" + `/${getuserselect = $("#getuserselect option:selected").val()}`;
            Http.get(`${url}`).then((data)=>{
                    this.AllMessages = data.data;

                $('.chat').empty();
                $.each(this.AllMessages, function (key,data) {
                    if(id == data.from_id ){
                        direction = "right";
                        $('.chat').append(`
                               <li class="${direction}">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>${ data.user_from.name }</strong>
                                    </div>
                                <p>${ data.body }</p>
                                </div>
                            </li>
                    `)
                    }
                    else{
                        direction = "left";
                        $('.chat').append(`
                               <li class="${direction}">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>${ data.user_from.name }</strong>
                                    </div>
                                <p>${ data.body }</p>
                                </div>
                            </li>
                    `)
                    }

                });
                this.session = {
                    'from_id' : this.AllMessages[0].from_id,
                    'to_id' : this.AllMessages[0].to_id,
                };
            });
        });


        $("#sendMessage").click(function(){
            getuserselect = $("#getuserselect option:selected").val();
            if(message.val() == ''){
                message.addClass('is-invalid');
            }else{
                Http.post("{{url('Private/messages')}}",{
                    'body' : message.val(),
                    'from_id' : id,
                    'to_id' : getuserselect = $("#getuserselect option:selected").val(),
                }).then(()=>{
                    message.val('');
                })
            }
        });

        let direction;
        if($("#getuserselect option:selected").val() != id){
            let channel1 = Echo.private(`Chat.1.2`);
            let channel2 = Echo.private(`Chat.2.1`);
            console.log(id);
            console.log($("#getuserselect option:selected").val());
            channel1.listen('PrivateChatEvent',function (data){
                console.log(data);
                if(id == data.message.from_id ){
                    direction = "right";
                    $('.chat').append(`
                               <li class="${direction}">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>${ data.message['user_from']['name'] }</strong>
                                    </div>
                                <p>${ data.message.body }</p>
                                </div>
                            </li>
                    `)
                }
                else{
                    direction = "left";
                    $('.chat').append(`
                               <li class="${direction}">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>${ data.message['user_from']['name'] }</strong>
                                    </div>
                                <p>${ data.message.body }</p>
                                </div>
                            </li>
                    `)
                }
            })
            channel2.listen('PrivateChatEvent',function (data){
                console.log(data);
                if(id == data.message.from_id ){
                    direction = "right";
                    $('.chat').append(`
                               <li class="${direction}">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>${ data.message['user_from']['name'] }</strong>
                                    </div>
                                <p>${ data.message.body }</p>
                                </div>
                            </li>
                    `)
                }
                else{
                    direction = "left";
                    $('.chat').append(`
                               <li class="${direction}">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>${ data.message['user_from']['name'] }</strong>
                                    </div>
                                <p>${ data.message.body }</p>
                                </div>
                            </li>
                    `)
                }
            })
        }else{
            let channel = Echo.private(`Chat.${id}.${id}`);

            channel.listen('PrivateChatEvent',function (data){
                console.log(data);
                console.log(id);
                console.log($("#getuserselect option:selected").val());
                if(id == data.message.from_id ){
                    direction = "right";
                    $('.chat').append(`
                               <li class="${direction}">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>${ data.message['user_from']['name'] }</strong>
                                    </div>
                                <p>${ data.message.body }</p>
                                </div>
                            </li>
                    `)
                }
                else{
                    direction = "left";
                    $('.chat').append(`
                               <li class="${direction}">
                                <div class="clearfix">
                                    <div class="header">
                                        <strong>${ data.message['user_from']['name'] }</strong>
                                    </div>
                                <p>${ data.message.body }</p>
                                </div>
                            </li>
                    `)
                }
            })
        }

    })
</script>
@endsection
