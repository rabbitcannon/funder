<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Funder</title>

        <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">

        <!-- PaySafe.js -->
        <script src="https://hosted.paysafe.com/js/v1/latest/paysafe.min.js"></script>
    </head>
<body>

    <div id="app"></div>
    <script type="text/javascript" src="{!! asset('js/app.js') !!}"></script>
    {{--@if(Auth::check())--}}
        {{--<div id="app"></div>--}}
        {{--<script type="text/javascript" src="{!! asset('js/app.js') !!}"></script>--}}
    {{--@else--}}
        {{--<div id="login-form"></div>--}}
        {{--<script type="text/javascript" src="{!! asset('js/LoginForm.js') !!}"></script>--}}

        {{--<div class="centered">--}}
            {{--<form action="/api/funding/login" method="post">--}}
                {{--<div class="card" style="width: 400px;">--}}
                    {{--<div class="card-divider">--}}
                        {{--Login--}}
                    {{--</div>--}}
                    {{--<div class="card-section">--}}
                        {{--<label>Email Address--}}
                            {{--<input type="text" name="email" placeholder="Email">--}}
                        {{--</label>--}}
                        {{--<input type="hidden" name="registrar_id" value="1">--}}
                        {{--<label>Password--}}
                            {{--<input type="password" name="password" placeholder="Password">--}}
                        {{--</label>--}}
                        {{--<div class="text-right">--}}
                            {{--<input type="submit" class="button" value="Login">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</form>--}}
        {{--</div>--}}
    {{--@endif--}}

    {{--<script type="text/javascript" src="{!! asset('js/app.js') !!}"></script>--}}

</body>
</html>