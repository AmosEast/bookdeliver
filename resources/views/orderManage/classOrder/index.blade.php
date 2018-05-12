@extends('layouts.baseFrame')
@section('content')
    @if($error == 1)
        <div id="error-msg-div" class="panel panel-danger" style="width: 100%;">
            <div class="panel-heading">
                <h3> 错误提示</h3>
            </div>
            <div class="panel-body" style="text-align: center;">
                <h4>{{ $msg }}</h4>
            </div>
        </div>
    @else
        {{--当前选书列表 --}}
        <div id="list-selects-div" class="panel panel-success" style="width: 100%; min-height: 100%;">
            <div class="panel-heading">
                <h3>班级选择</h3>
            </div>
            <div class="panel-body table-responsive">
                <form id="class_order_form" action="{{ route('classorder.orderbooksview') }}" method="post">
                    @csrf
                    <input type="hidden" name="select_academy" value="{{ $academy ->id }}">
                    <div class="form-group">
                        <label for="select_task">任务选择</label>
                        <select class="form-control" name="select_task" id="select_task">
                            @foreach($tasks as $task)
                                <option value="{{ $task ->id }}">{{ $task ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="select_major">专业选择</label>
                        <select class="form-control" name="select_major" id="select_major" onclick="selectOnChange('select_major', 'select_class', '{{ $jsonClassesInfo }}')">
                            @foreach($majors as $major)
                                <option value="{{ $major ->id }}">{{ $major ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="select_class">班级选择</label>
                        <select class="form-control selectpicker dropup" name="select_class" id="select_class" title="请选择班级">
                        </select>
                    </div>
                    <input class="btn btn-primary" type="submit" value="提交">
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
    <script type="text/javascript">
        {{-- 弹窗关闭按钮点击时的函数回调 --}}
        function clickXFunc() {
            window.location.reload();
        }
    </script>
@endsection
