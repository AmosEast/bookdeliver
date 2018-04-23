@extends('layouts.baseFrame')
@section('content')
    <div id="add-course-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加课程</h3>
        </div>
        <div class="panel-body">
            <form id="add-course-form" method="post" action="{{ route('coursesmanage.addcourse') }}" class="form-inline" onsubmit="return false;">
                @csrf
                <div class="form-group">
                    <label class="sr-only" for="course-name">课程名称</label>
                    <input type="text" class="form-control" id="course-name" name="course_name" placeholder="请输入课程名">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="course-description">课程简介</label>
                    <input type="text" class="form-control" id="course-name" name="course_description" placeholder="请输入课程简介">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="course-majors">所属专业</label>
                    <select name="course_majors[]" id="course-majors" class="selectpicker show-menu-arrow form-control bs-select-hidden" multiple="multiple" data-live-search="true" title="请选择课程所属专业">
                        @foreach($majors as $major)
                            <option value="{{ $major ->id }}">{{ $major ->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('add-course-form')">&nbsp;添&nbsp;加&nbsp;</button>
            </form>
        </div>
    </div>
    <div id="list-course-div" class="panel panel-success" style="width: 100%;">
        <div class="panel-heading">
            <h3>课程列表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table id="list-course-table" class="table table-striped table-hover">
                <tr>
                    <th>#</th><th>课程名</th><th>课程描述</th><th>所属专业</th><th>状态</th><th>更新时间</th><th>更新人</th><th>操作</th>
                </tr>
                @if(!empty($courses))
                    @foreach($courses as $course)
                        <tr>
                            <td>{{ $course ->id }}</td>
                            <td>{{ $course ->name }}</td>
                            <td>{{ $course ->description }}</td>
                            <td><a href="javascript:void(0);" class="btn btn-link brn-xs" onclick="ajaxASubmitToShowData('{{ route('coursesmanage.getmajors', ['courseId' =>$course ->id]) }}', '<br />')">详情</a></td>
                            <td>{{ $course ->is_valid == 1 ? '启用':'弃用' }}</td>
                            <td>{{ $course ->updated_at }}</td>
                            <td>{{ $course ->updater ->name }}</td>
                            <td><a href="javascript:void(0);" class="btn btn-primary brn-xs" onclick="popIframeWithCloseFunc('编辑课程信息', '{{ route('coursesmanage.editcourseview', ['courseId' =>$course ->id]) }}', '675px', '535px', clickXFunc)">编辑</a></td>
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
