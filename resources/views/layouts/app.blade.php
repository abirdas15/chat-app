<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        ul{
            margin: 0;
            padding: 0;
        }
        li{
            list-style: none;
        }
        .user-wrapper, .message-wrapper{
            border: 1px solid #dddddd;
            overflow-y: auto;
        }
        .user-wrapper{
            height: 600px;
        }
        .user{
            cursor: pointer;
            padding: 5px 0;
            position: relative;
        }
        .user:hover{
            background: #eeeeee;
        }
        .user:last-child{
            margin-bottom: 0;
        }
        .pending{
            position: absolute;
            left: 13px;
            top: 9px;
            background: #b60ffb;
            margin: 0;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            line-height: 18px;
            padding-left: 5px;
            color: #ffffff;
            font-size: 12px;
        }
        .media-left{
            margin: 0 10px;
        }
        .media-left img{
            width: 64px;
            border-radius: 64px;
        }
        .media-body p{
            margin: 6px 0;
        }
        .message-wrapper{
            height: 536px;
            padding: 10px;
            background: #eeeeee;
        }
        .messages, .message{
            margin-bottom: 15px;
        }
        .messages, .message:last-child{
            margin-bottom: 0;
        }
        .sent, .receive{
            width: 45%;
            padding: 3px 10px;
            border-radius: 10px;
        }
        .receive{
            background: #ffffff;
        }
        .sent{
            background: #3bebff;
            float: right;
            text-align: right;
        }
        .message p{
            margin: 5px 0;
        }
        .date{
            color: #777777;
            font-size: 12px;
        }
        .active{
            background: #eeeeee;
        }
        input[type=text]{
            width: 100%;
            padding: 12px 20px;
            margin: 15px 0 0 0;
            display: inline-block;
            border-radius: 4px;
            box-sizing: border-box;
            outline: none;
            border: 1px solid #cccccc;
        }
        input[type=text]:focus{
            border: 1px solid #aaaaaa;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        var receiver_id = '';
        var my_id = "{{ Auth::id() }}";
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

             // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;

            var pusher = new Pusher('c4f7fb3b547c9b4cb04e', {
            cluster: 'ap2',
            forceTLS: true
            });

            var channel = pusher.subscribe('my-channel');
            channel.bind('my-event', function(data) {
                if(my_id == data.from){
                    $("#"+data.to).click();
                }else if(my_id==data.to){
                    if(receiver_id == data.from){
                        $("#"+data.from).click();
                    }else{
                        var pending = parseInt($("#"+data.from).find('.pending').html());
                        if(pending){
                            $("#"+data.from).find('.pending').html(pending+1);
                        }else{
                            $("#"+data.from).append('<span class="pending">1</span>');
                        }
                    }
                }
            });

            $(".user").click(function(){
                $(".user").removeClass('active');
                $(this).addClass('active');
                $(this).find(".pending").remove();
                receiver_id = $(this).attr('id');
                $.ajax({
                    type:"GET",
                    url:"message/"+receiver_id,
                    data:"",
                    cache:false,
                    success:function(data){
                        $("#message").html(data);
                        scrollToBottomFunc();
                    }
                });
            });
            $(document).on('keyup','.input-text input',function(event){
                event.preventDefault();
                var message = $(this).val();
                if(event.keyCode==13 && message !='' && receiver_id !=''){
                    $(this).val("");
                    var dataStr = "receiver_id="+receiver_id+"&message="+message;
                    $.ajax({
                        type:"POST",
                        url:"message",
                        data:dataStr,
                        cache:false,
                        success:function(data){
                        },
                        error:function(){

                        },
                        complete:function(){
                            scrollToBottomFunc();
                        }
                    });
                }
            });
        });
        function scrollToBottomFunc(){
            $(".message-wrapper").animate({
                scrollTop:$(".message-wrapper").get(0).scrollHeight
            },50);
        }
    </script>
</body>
</html>
