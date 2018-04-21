@extends('layouts.baseFrame')
@section('content')
    <div id="add-academy-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加学院</h3>
        </div>
        <div class="panel-body">
            <form id="add-academy-form" method="post" action="{{ route('academiesmanage.addacademy') }}" class="form-inline" onsubmit="return false;">
                @csrf
                <div class="form-group">
                    <label class="sr-only" for="academy-unique-id">学院编码</label>
                    <input type="text" class="form-control" id="academy-unique-id" name="academy_unique_id" placeholder="请输入学院编码">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="academy-name">学院名称</label>
                    <input type="text" class="form-control" id="academy-name" name="academy_name" placeholder="请输入学院名">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="academy-description">学院简介</label>
                    <input type="text" class="form-control" id="academy-name" name="academy_description" placeholder="请输入学院简介">
                </div>
                <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('add-academy-form')">&nbsp;添&nbsp;加&nbsp;</button>
            </form>
        </div>
    </div>
    <div id="list-academy-div" class="panel panel-success" style="width: 100%;">
        <div class="panel-heading">
            <h3>学院列表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table id="list-academy-table" class="table table-striped table-hover">
                <tr>
                    <th>编码</th><th>学院名</th><th>学院描述</th><th>状态</th><th>创建时间</th><th>更新时间</th><th>更新人</th><th>操作</th>
                </tr>
                @if(!empty($academies))
                    @foreach($academies as $academy)
                        <tr>
                            <td>{{ $academy ->unique_id }}</td>
                            <td>{{ $academy ->name }}</td>
                            <td>{{ $academy ->description }}</td>
                            <td>{{ $academy ->is_valid == 1 ? '启用':'弃用' }}</td>
                            <td>{{ $academy ->created_at }}</td>
                            <td>{{ $academy ->updated_at }}</td>
                            <td>{{ $academy ->updater }}</td>
                            <td><a href="javascript:void(0);" class="btn btn-primary brn-xs" onclick="popIframeWithCloseFunc('编辑学院信息', '{{ route('academiesmanage.editacademyview', ['academyId' =>$academy ->id]) }}', '675px', '535px', clickXFunc)">编辑</a></td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
@endsection

@section('js-text-part')
    <script type="text/javascript">
        {{-- 弹窗关闭按钮点击时的函数回调 --}}
        function clickXFunc() {
            window.location.reload();
        }
    </script>
@endsection
