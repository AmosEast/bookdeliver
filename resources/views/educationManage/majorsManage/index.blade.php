@extends('layouts.baseFrame')
@section('content')
    <div id="add-major-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加专业</h3>
        </div>
        <div class="panel-body">
            <form id="add-major-form" method="post" action="{{ route('majorsmanage.addmajor') }}" class="form-inline" onsubmit="return false;">
                @csrf
                <div class="form-group">
                    <label class="sr-only" for="major-unique-id">专业编码</label>
                    <input type="text" class="form-control" id="major-unique-id" name="major_unique_id" placeholder="请输入专业编码">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="major-name">专业名称</label>
                    <input type="text" class="form-control" id="major-name" name="major_name" placeholder="请输入专业名">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="major-description">专业简介</label>
                    <input type="text" class="form-control" id="major-name" name="major_description" placeholder="请输入专业简介">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="major-academy-id">所属院系</label>
                    <select name="major_academy_id" id="major-academy-id" class="form-control">
                        @foreach($academies as $academy)
                            <option value="{{ $academy ->id }}">{{ $academy ->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('add-major-form')">&nbsp;添&nbsp;加&nbsp;</button>
            </form>
        </div>
    </div>
    <div id="list-major-div" class="panel panel-success" style="width: 100%;">
        <div class="panel-heading">
            <h3>专业列表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table id="list-major-table" class="table table-striped table-hover">
                <tr>
                    <th>#</th><th>编码</th><th>专业名</th><th>专业描述</th><th>状态</th><th>所属院系</th><th>更新时间</th><th>更新人</th><th>操作</th>
                </tr>
                @if(!empty($majors))
                    @foreach($majors as $major)
                        <tr>
                            <td>{{ $major ->id }}</td>
                            <td>{{ $major ->unique_id }}</td>
                            <td>{{ $major ->name }}</td>
                            <td>{{ $major ->description }}</td>
                            <td>{{ $major ->is_valid == 1 ? '启用':'弃用' }}</td>
                            <td>{{ $major ->academy ->name }}</td>
                            <td>{{ $major ->updated_at }}</td>
                            <td>{{ $major ->updater ->name }}</td>
                            <td><a href="javascript:void(0);" class="btn btn-primary brn-xs" onclick="popIframeWithCloseFunc('编辑专业信息', '{{ route('majorsmanage.editmajorview', ['majorId' =>$major ->id]) }}', '675px', '535px', clickXFunc)">编辑</a></td>
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
