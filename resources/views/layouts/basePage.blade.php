<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>教务领书系统</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- css -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">

    <!-- js -->
    <script type="javascript" src="/js/jquery-3.3.1.min.js"></script>
    <script type="javascript"  src="/js/bootstrap.min.js"></script>
    <script type="javascript" src="/js/app.js"></script>
</head>
<body>
<!-- 存放主页面 -->
<div class = "" style="width: 100%;">
    <!-- 主页面 -->
    @yield('content')

    <!-- js -->
    @yield('textJs')
</div>
</body>
</html>
