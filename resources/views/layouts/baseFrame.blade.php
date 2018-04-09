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
    <script src="{{ asset('js/layer/layer.js') }}" defer></script>
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
<div id="app" class="container-fluid" style="padding-left: 0; padding-right: 0; background-color: #f9f9f9; min-height: 600px;">
    <!-- 主页面 -->
    <div id="main" class="container-fluid" style="height: 100%; margin-top: 20px;">
        @yield('content')
    </div>
</div>

<script type="text/javascript">
    {{-- ajax提交表单方法 --}}
    function ajaxFormSubmit(formId) {
        var form = $("#" + formId);
        $.ajax({
            url: form.attr("action"),
            async: false,
            type: form.attr("method"),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: form.serialize(),
            dataType: "json",
            error: function () {

            },
            success: function (data) {
                if(data.error == false) {
                    layer.alert('操作成功！', {icon: 1}, function (index) {
                        window.location.reload();
                        layer.close();
                    });
                }
                else {
                    var msg = "操作失败！<br />";
                    for(var i in data.msg) {
                        msg += data.msg[i] + "<br />";
                    }
                    layer.alert(msg, {icon: 2});
                }
            }
        });
        return false;
    }
    {{-- ajax提交a标签方法 --}}
    function ajaxASubmit(requestUrl) {
        $.ajax({
            url: requestUrl,
            async: false,
            type: "get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            dataType: "json",
            error: function () {

            },
            success: function (data) {
                if(data.error == false) {
                    layer.alert('操作成功！', {icon: 1}, function (index) {
                        window.location.reload();
                        layer.close();
                    });
                }
                else {
                    var msg = "操作失败！<br />";
                    for(var i in data.msg) {
                        msg += data.msg[i] + "<br />";
                    }
                    layer.alert(msg, {icon: 2});
                }
            }
        });
        return false;
    }
    {{-- 弹窗页面 --}}
    function popIframe(titleStr, frameUrl, width, height) {
        layer.open({
            type: 2,
            title: [titleStr, 'font-size:1.2em;'],
            content: frameUrl,
            area: [width, height],
        });
    }
    {{-- 带关闭回调函数的弹窗页面 --}}
    function popIframeWithCloseFunc(titleStr, frameUrl, width, height, closeFunc) {
        layer.open({
            type: 2,
            title: [titleStr, 'font-size:1.2em;'],
            content: frameUrl,
            area: [width, height],
            cancel: function (index, layero) {
                closeFunc();
                layer.close();
            }
        });
    }
</script>
@yield('js-text-part')

</body>
</html>
