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
        <div id="edit-selector-div" class="panel panel-primary" style="width: 100%;">
            <div class="panel-heading">
                <h3>编辑选书分配</h3>
            </div>
            <div class="panel-body">
                <form id="edit-selector-form" method="post" action="{{ route('tasksmanage.editselectlist', ['selectId' =>$selectList ->id]) }}" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label for="select_task">任务</label>
                        <select name="select_task" id="select_task" class="form-control" title="请选择任务">
                            @foreach($tasks as $task)
                                @if($task ->status == \App\Models\Task::$select_process)
                                    <option value="{{ $task ->id }}" @if($selectList ->task_id == $task ->id)selected @endif>{{ $task ->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="select_grade">年级</label>
                        <select name="select_grade" id="select_grade" class="selectpicker form-control" title="请选择年级">
                            @foreach($grades as $grade)
                                <option value="{{ $grade }}" @if($selectList ->grade == $grade)selected @endif>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="select_major">专业</label>
                        <select name="select_major" id="select_major" class="selectpicker form-control" title="请选择专业" onchange="selectOnChange('select_major', 'select_course', '{{$jsonMajorCourses}}')">
                            @foreach($majors as $major)
                                <option value="{{ $major ->id }}" @if($selectList ->major_id == $major ->id)selected @endif>{{ $major ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="select_course">课程</label>
                        <select name="select_course" id="select_course" class="selectpicker form-control" title="请选择课程" data-live-search="true">
                            @foreach($arrMajorCourses[$selectList ->major_id] as $course)
                                <option value="{{ $course ->id }}" @if($selectList ->course_id == $course ->id)selected @endif>{{ $course ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="select_user">选书人</label>
                        <select name="select_user" id="select_user" class="selectpicker form-control" title="请选择选书人" data-live-search="true">
                            @foreach($users as $user)
                                <option value="{{ $user ->id }}" @if($user ->id == $selectList ->selector_id)selected @endif>{{ $user ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group has-warning">
                        <label for="select_valid">状态</label>
                        <select name="select_valid" id="select_valid" class="form-control">
                            <option value="1" selected>启用</option>
                            <option value="0">弃用</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('edit-selector-form')">&nbsp;修&nbsp;改&nbsp;</button>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
