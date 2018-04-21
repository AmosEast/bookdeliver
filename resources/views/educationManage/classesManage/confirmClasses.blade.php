@extends('layouts.baseFrame')
@section('content')
    @if($error == 1)
        <div id="error-msg-div" class="panel panel-danger" style="width: 100%;">
            <div class="panel-heading">
                <h3> 错误提示</h3>
            </div>
            <div class="panel-body" style="text-align: center;">
                @foreach($msg as $v)
                    <h4>{{ $v }}</h4>
                @endforeach
            </div>
        </div>
    @else
        <div id="list-class-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>Excel中班级信息</h3>
            </div>
            <div class="panel-body table-responsive">
                <table id="list-class-table" class="table table-striped table-hover">
                    <tr>
                        <th>#</th><th>编码</th><th>班级名</th><th>班级描述</th><th>所属专业</th><th>年级</th>
                    </tr>
                    @if(!empty($classes))
                        @foreach($classes as $key =>$class)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $class['class_unique_id'] }}</td>
                                <td>{{ $class['class_name'] }}</td>
                                <td>{{ $class['class_description'] }}</td>
                                <td>{{ $class['class_major_name'] }}</td>
                                <td>{{ $class['class_grade'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </table>
                <a href="javascript:void(0);" class="btn btn-primary brn-xs" style="float: right; margin-right: 8%;" onclick="ajaxASubmitWithCallback('{{ route('classesmanage.addclassesfromsession') }}', redirectTo, redirectTo)">确认添加</a>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
    <script type="text/javascript">
        {{-- 点击确认后跳转操作 --}}
        function redirectTo() {
            var redirectUrl = '{{ route('classesmanage.index') }}';
            window.location = redirectUrl;
        }
    </script>
@endsection
