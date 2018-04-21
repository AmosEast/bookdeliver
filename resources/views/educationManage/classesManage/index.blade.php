@extends('layouts.baseFrame')
@section('content')
    <div id="add-class-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加班级</h3>
        </div>
        <div class="panel-body">
            <h4><strong>添加单个班级</strong></h4>
            <form id="add-class-form" method="post" action="{{ route('classesmanage.addclass') }}" class="form-inline" onsubmit="return false;">
                @csrf
                <div class="form-group">
                    <label class="sr-only" for="class-unique-id">班级编码</label>
                    <input type="text" class="form-control" id="class-unique-id" name="class_unique_id" placeholder="请输入班级编码">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="class-name">班级名称</label>
                    <input type="text" class="form-control" id="class-name" name="class_name" placeholder="请输入班级名">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="class-description">班级简介</label>
                    <input type="text" class="form-control" id="class-name" name="class_description" placeholder="请输入班级简介">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="class-major-id">所属专业</label>
                    <select name="class_major_id" id="class-major-id" class="form-control">
                        @foreach($majors as $major)
                            <option value="{{ $major ->id }}">{{ $major ->name }}</option>
                        @endforeach
                    </select>
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="class-grade">年级</label>
                    <select name="class_grade" id="class-grade" class="form-control">
                        @foreach($grades as $grade)
                            <option value="{{ $grade }}">{{ $grade }}级</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('add-class-form')">&nbsp;添&nbsp;加&nbsp;</button>
            </form>

            <h4><strong>批量导入班级</strong></h4>
            <form id="add-class-form" method="post" action="{{ route('classesmanage.uploadclasses') }}" class="form-inline" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="sr-only" for="class-major-id">所属专业</label>
                    <select name="class_major_id" id="class-major-id" class="form-control">
                        @foreach($majors as $major)
                            <option value="{{ $major ->id }}">{{ $major ->name }}</option>
                        @endforeach
                    </select>
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="class-file">班级名单Excel</label>
                    <input type="file" id="class-file" name="class_file">
                </div>
                <button type="submit" class="btn btn-primary">&nbsp;添&nbsp;加&nbsp;</button>
                <a href="{{ route('classesmanage.downloadexcelexample') }}" class="btn btn-link">查看班级名单excel样例</a>
            </form>
        </div>
    </div>
    <div id="list-class-div" class="panel panel-success" style="width: 100%;">
        <div class="panel-heading">
            <h3>班级列表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table id="list-class-table" class="table table-striped table-hover">
                <tr>
                    <th>#</th><th>编码</th><th>班级名</th><th>班级描述</th><th>状态</th><th>所属专业</th><th>年级</th><th>更新时间</th><th>更新人</th><th>操作</th>
                </tr>
                @if(!empty($classes))
                    @foreach($classes as $class)
                        <tr>
                            <td>{{ $class ->id }}</td>
                            <td>{{ $class ->unique_id }}</td>
                            <td>{{ $class ->name }}</td>
                            <td>{{ $class ->description }}</td>
                            <td>{{ $class ->is_valid == 1 ? '启用':'弃用' }}</td>
                            <td>{{ $class ->major ->name }}</td>
                            <td>{{ $class ->grade }}</td>
                            <td>{{ $class ->updated_at }}</td>
                            <td>{{ $class ->updater ->name }}</td>
                            <td><a href="javascript:void(0);" class="btn btn-primary brn-xs" onclick="popIframeWithCloseFunc('编辑班级信息', '{{ route('classesmanage.editclassview', ['classId' =>$class ->id]) }}', '675px', '535px', clickXFunc)">编辑</a></td>
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
