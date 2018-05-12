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
    <script src="{{ asset('js/bootstrap-select.min.js') }}" defer></script>
@yield('js-link-part')

<!-- Fonts -->
    {{--<link rel="dns-prefetch" href="https://fonts.gstatic.com">--}}
    {{--<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">--}}

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-select.min.css') }}" rel="stylesheet">
    @yield('css-link-part')
    <style>
        div{
            padding: 0;
            margin: 0;
        }
        .table-center{
            text-align: center !important;
            vertical-align: middle !important;
        }
        .table-center th{
            text-align: center !important;
            vertical-align: middle !important;
        }
        .table-center td {
            text-align: center !important;
            vertical-align: middle !important;
        }
        .table-border-my {
            border: 1px solid gray !important;
        }
        .table-border-my tr th{
            border: 1px solid gray !important;
        }
        .table-border-my tr td{
            border: 1px solid gray !important;
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
    {{-- ajax提交表单方法后带回调方法 --}}
    function ajaxFormSubmitWithCallback(formId, successCallbackFunc, failCallbackFunc) {
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
                        layer.close();
                        successCallbackFunc();
                    });
                }
                else {
                    var msg = "操作失败！<br />";
                    for(var i in data.msg) {
                        msg += data.msg[i] + "<br />";
                    }
                    layer.alert(msg, {icon: 2}, function (index) {
                        layer.close();
                        failCallbackFunc();
                    });
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
    {{-- ajax提交a标签后带回调方法 --}}
    function ajaxASubmitWithCallback(requestUrl, successCallbackFunc, failCallbackFunc) {
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
                        layer.close();
                        successCallbackFunc();
                    });
                }
                else {
                    var msg = "操作失败！<br />";
                    for(var i in data.msg) {
                        msg += data.msg[i] + "<br />";
                    }
                    layer.alert(msg, {icon: 2}, function (index) {
                        layer.close();
                        failCallbackFunc();
                    });
                }
            }
        });
        return false;
    }
    {{-- ajax提交a标签来展示数据 --}}
    function ajaxASubmitToShowData(requestUrl, glue) {
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
                    var dataMsg = "";
                    for(var i in data.data) {
                         dataMsg += data.data[i] + glue;
                    }
                    layer.alert(dataMsg, function (index) {
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
    {{-- 在确认信息后提交a标签 --}}
    function ajaxASubmitAfterConfirm(msg, requestUrl) {
        layer.confirm(msg, {icon: 3, title:'提示'}, function (index) {
            layer.close(index);
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
        })
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
    {{-- select的两级联动方法 --}}
    function selectOnChange(firstSelectId, secondSelectId, selectData) {
        var jsonObj = $.parseJSON(selectData);
        var index = $('#' + firstSelectId).val();
        var secSelOptions = '';
        for(var i in jsonObj[index]) {
            secSelOptions += "<option value='" + jsonObj[index][i].id + "'>" + jsonObj[index][i].name + "</option>";
        }
        $('#' + secondSelectId).empty();
        $('#' + secondSelectId).append(secSelOptions);
        $('#' + secondSelectId).selectpicker('refresh');
    }
    {{-- 实现checkbox做全选和全不选的方法 controlId必须是一个checkbox --}}
    function selectAll(controlId, oddName) {
        var controlBox = $('#' + controlId);
        if(controlBox.is(':checked')) {
            $('input[name="' + oddName + '"]').each(function () {
                $(this).prop('checked', true);
            })
        }
        else {
            $('input[name="' + oddName + '"]').each(function () {
                $(this).prop('checked', false);
            })
        }
    }
</script>
@yield('js-text-part')

</body>
</html>
