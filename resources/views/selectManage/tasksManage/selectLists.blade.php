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
        {{--指定选书人模块 --}}
        @if(Auth::user() ->can('tasksmanage@addtask') && count($tasks) > 0 && $academyId)
            <div id="add-selector-div" class="panel panel-primary" style="width: 100%;">
                <div class="panel-heading">
                    <h3>添加选书人</h3>
                </div>
                <div class="panel-body">
                    <form id="add-selector-form" method="post" action="{{ route('tasksmanage.setselector') }}" class="form-inline" onsubmit="return false;">
                        @csrf
                        <input type="hidden" name="select_academy" value="{{ $academyId }}">
                        <div class="form-group">
                            <label class="sr-only" for="select_task">任务选择</label>
                            <select name="select_task" id="select_task" class="form-control" title="请选择任务">
                                @foreach($tasks as $task)
                                    @if($task ->status == \App\Models\Task::$select_process)
                                        <option value="{{ $task ->id }}">{{ $task ->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <span>-</span>
                        <div class="form-group">
                            <label class="sr-only" for="select_grade">年级选择</label>
                            <select name="select_grade" id="select_grade" class="selectpicker form-control" title="请选择年级">
                                @foreach($grades as $grade)
                                    <option value="{{ $grade }}">{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span>-</span>
                        <div class="form-group">
                            <label class="sr-only" for="select_major">专业选择</label>
                            <select name="select_major" id="select_major" class="selectpicker form-control" title="请选择专业" onchange="selectOnChange('select_major', 'select_course', '{{$jsonMajorCourses}}')">
                                @foreach($majors as $major)
                                    <option value="{{ $major ->id }}">{{ $major ->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span>-</span>
                        <div class="form-group">
                            <label class="sr-only" for="select_course">课程选择</label>
                            <select name="select_course" id="select_course" class="selectpicker form-control" title="请选择课程" data-live-search="true">
                            </select>
                        </div>
                        <span>-</span>
                        <div class="form-group">
                            <label class="sr-only" for="select_user">选书人选择</label>
                            <select name="select_user" id="select_user" class="selectpicker form-control" title="请选择选书人" data-live-search="true">
                                @foreach($users as $user)
                                    <option value="{{ $user ->id }}">{{ $user ->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('add-selector-form')">&nbsp;添&nbsp;加&nbsp;</button>
                    </form>
                </div>
            </div>

        @endif
        {{--当前任务模块 --}}
        <div id="list-selectlist-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>当前选书安排情况</h3>
            </div>
            <div class="panel-body table-responsive">
                <table id="list-task-table" class="table table-striped table-hover">
                    <tr>
                        <th>#</th><th>任务名称</th><th>年级</th><th>专业名称</th><th>课程名称</th><th>选书教师</th><th>选书情况</th><th>更新时间</th><th>操作</th>
                    </tr>
                    @if(!empty($selectLists))
                        @foreach($tasks as $task)
                            @foreach($selectLists[$task ->id] as $selectList)
                                <tr>
                                    <td>{{ $selectList ->id }}</td>
                                    <td>{{ $arrTasks[$selectList ->task_id] ->name }}</td>
                                    <td>{{ $selectList ->grade }}</td>
                                    <td>{{ $arrMajors[$selectList ->major_id] ->name }}</td>
                                    <td>{{ $arrCourses[$selectList ->course_id] ->name }}</td>
                                    <td>{{ $arrUsers[$selectList ->selector_id] ->name }}</td>
                                    <td class = "{{ $selectList ->book_ids ? '':'bg-danger' }}">{{ $selectList ->book_ids ? '已选择' : '未选择' }}</td>
                                    <td>{{ $selectList ->updated_at }}</td>
                                    <td><a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="popIframeWithCloseFunc('编辑选书分配', '{{ route('tasksmanage.editselectlistview', ['selectId' =>$selectList->id]) }}', '675px', '535px', clickXFunc)">编辑</a></td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endif
                </table>
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
