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

    <div>
        <div class="container"></div>

        <div class="main">
            <div id="app"></div>
            <script type="text/javascript" src="{!! asset('js/app.js') !!}"></script>
        </div>
    </div>

    <footer class="__url">
        <div id="preloader">
            <div id="status">&nbsp;</div>
        </div>
    
        <script>
            $(window).on('load', function() {
                $('#status').fadeOut();
                $('#preloader').delay(350).fadeOut('slow');
                $('.container').css('display', 'block');
                $('body').delay(350).css({'overflow':'visible'});
            });
        </script>

        <small>
            Logging into: {{ $url }}
        </small>
    </footer>
</body>
</html>