@extends('layouts.baseFrame')
@section('content')
    {{--添加任务模块 --}}
    @can('tasksmanage@addtask')
        <div id="add-task-div" class="panel panel-primary" style="width: 100%;">
            <div class="panel-heading">
                <h3>添加任务</h3>
            </div>
            <div class="panel-body">
                <form id="add-task-form" method="post" action="{{ route('tasksmanage.addtask') }}" class="form-inline" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label class="sr-only" for="task-name">任务名称</label>
                        <input type="text" class="form-control" id="task-name" name="task_name" placeholder="请输入任务名称">
                    </div>
                    <span>-</span>
                    <div class="form-group">
                        <label class="sr-only" for="task-description">任务简介</label>
                        <input type="text" class="form-control" id="task-description" name="task_description" placeholder="请输入任务简介">
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('add-task-form')">&nbsp;添&nbsp;加&nbsp;</button>
                </form>
            </div>
        </div>
    @endcan
    {{--当前任务模块 --}}
    <div id="list-task-div" class="panel panel-success" style="width: 100%;">
        <div class="panel-heading">
            <h3>当前任务列表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table id="list-task-table" class="table table-striped table-hover">
                <tr>
                    <th>#</th><th>名称</th><th>描述</th><th>当前状态</th><th>更新时间</th><th>更新人</th>@can('tasksmanage@changetaskstatus')<th>开启下一状态</th>@endcan
                </tr>
                @if(!empty($tasks))
                    @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task ->id }}</td>
                            <td>{{ $task ->name }}</td>
                            <td>{{ $task ->description }}</td>
                            <td>{{ $statusMeaning[$task ->status] }}</td>
                            <td>{{ $task ->updated_at }}</td>
                            <td>{{ $task ->updater ->name }}</td>
                            @can('tasksmanage@changetaskstatus')
                                <td><a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="ajaxASubmitAfterConfirm('{{ $statusTips[$task ->status + 1] }}', '{{ route('tasksmanage.changetaskstatus', ['taskId' =>$task ->id, 'status' =>$task ->status + 1]) }}')">{{ $statusMeaning[$task ->status + 1] }}</a></td>
                            @endcan
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
