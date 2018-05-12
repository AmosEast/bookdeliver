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
        <div id="list-selectlist-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>选书列表</h3>
            </div>
            <div class="panel-body table-responsive">
                <div class="panel">
                    <div class="panel-body">
                        <form id="query-selectlists-form" method="get" action="{{ route('teacherorder.index') }}" class="form-inline">
                            @csrf
                            <div class="form-group">
                                <label class="sr-only" for="select_task">任务选择</label>
                                <select name="select_task" id="select_task" class="form-control" title="请选择任务">
                                    <option value="0">请选择任务</option>
                                    @foreach($arrTasksInfo as $task)
                                        <option value="{{ $task ->id }}" @if(old('select_task') == $task ->id)selected @endif>{{ $task ->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span>-</span>
                            <div class="form-group">
                                <label class="sr-only" for="select_has_ordered">购书状态选择</label>
                                <select name="select_has_ordered" id="select_has_ordered" class="form-control" title="请选择购书状态">
                                    <option value="0">请选择购书状态</option>
                                    <option value="1" @if(old('select_has_ordered') == 1) selected @endif>已购书籍</option>
                                    <option value="-1" @if(old('select_has_ordered') == -1) selected @endif>未购书籍</option>
                                </select>
                            </div>
                            <span>-</span>
                            <div class="form-group">
                                <label class="sr-only" for="select_grade">年级选择</label>
                                <select name="select_grade" id="select_grade" class="form-control" title="请选择年级">
                                    <option value="0">请选择年级</option>
                                    @foreach($grades as $grade)
                                        <option value="{{ $grade }}" @if(old('select_grade') == $grade)selected @endif>{{ $grade }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span>-</span>
                            <div class="form-group">
                                <label class="sr-only" for="select_major">专业选择</label>
                                <select name="select_major" id="select_major" class="form-control" title="请选择专业">
                                    <option value="0">请选择专业</option>
                                    @foreach($arrMajorsInfo as $major)
                                        <option value="{{ $major ->id }}" @if(old('select_major') == $major ->id)selected @endif>{{ $major ->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span>-</span>
                            <div class="form-group">
                                <label class="sr-only" for="select_course">课程选择</label>
                                <select name="select_course" id="select_course" class="selectpicker form-control" title="请选择课程" data-live-search="true">
                                    <option value="0">请选择课程</option>
                                    @foreach($arrCoursesInfo as $course)
                                        <option value="{{ $course ->id }}" @if(old('select_course') == $course ->id)selected @endif>{{ $course ->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">&nbsp;搜&nbsp;索&nbsp;</button>
                        </form>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-body table-responsive">
                        <table id="list-task-table" class="table table-striped table-hover">
                            <tr>
                                <th>#</th><th>任务名称</th><th>年级</th><th>学院名称</th><th>专业名称</th><th>课程名称</th><th>已购书籍</th><th>书籍订购</th>
                            </tr>
                            @if(!empty($selectLists))
                                @foreach($selectLists as $selectList)
                                    <tr>
                                        <td>{{ $selectList ->id }}</td>
                                        <td>{{ $arrTasksInfo[$selectList ->task_id] ->name }}</td>
                                        <td>{{ $selectList ->grade }}</td>
                                        <td>{{ $academy ->name }}</td>
                                        <td>{{ $arrMajorsInfo[$selectList ->major_id] ->name }}</td>
                                        <td>{{ $arrCoursesInfo[$selectList ->course_id] ->name }}</td>
                                        <td>
                                            @if(isset($arrOrdersInfo[$selectList ->id]) && !empty($arrOrdersInfo[$selectList ->id]))
                                                @foreach($arrOrdersInfo[$selectList ->id] as $order)
                                                    {{ $order ->book ->name }}X{{ $order ->quantity }}<br />
                                                @endforeach
                                            @endif
                                        </td>
                                        <td><a class="btn btn-primary btn-sm" href="javascript:void(0)" onclick="popIframeWithCloseFunc('书籍订购', '{{route('teacherorder.orderbooksview', ['$selectList' =>$selectList ->id])}}', '1015px', '515px', clickXFunc)">书籍订购</a></td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    </div>
                    <div align="right">
                        {{ $selectLists ->appends(['select_task' =>old('select_task'), 'select_has_ordered' =>old('select_has_ordered'), 'select_grade' =>old('select_grade'), 'select_major' =>old('select_major'), 'select_course' =>old('select_course')]) ->links() }}
                    </div>
                </div>
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
        function changeFormActionBeforeSubmit(formId ,url) {
            var form = $('#' + formId);
            form.attr('action', url);
            ajaxFormSubmit(formId);
        }
    </script>
@endsection
