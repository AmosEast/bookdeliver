<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.3.1.min.js') }}" defer></script>
    <script src="{{ asset('js/bootstrap.min.js') }}" defer></script>
    @yield('js-link-part')

<!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    @yield('css-link-part')
    <style>
        div{
            padding: 0;
            margin: 0;
        }
    </style>
    @yield('css-text-part')
</head>
<body>
<div id="app" class="container-fluid" style="padding-left: 0; padding-right: 0; background-color: #f4f8fb;">
    <!-- 主页面 -->
    <div id="main" class="container-fluid">
        @yield('content')
    </div>
</div>

@yield('js-text-part')

</body>
</html>
