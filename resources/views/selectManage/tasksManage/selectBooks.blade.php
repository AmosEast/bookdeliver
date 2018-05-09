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
        {{--当前任务模块 --}}
        <div id="list-selectlist-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>当前选书情况</h3>
            </div>
            <div class="panel-body table-responsive">
                <table id="list-task-table" class="table table-striped table-hover">
                    <tr>
                        <th>#</th><th>任务名称</th><th>年级</th><th>专业名称</th><th>课程名称</th><th>教材类书籍</th><th>教参类书籍</th><th>审核状态</th><th>操作</th>
                    </tr>
                    @if(!empty($selectLists))
                        @foreach($selectLists as $selectList)
                            <tr>
                                <td>{{ $selectList ->id }}</td>
                                <td>{{ $arrTasksInfo[$selectList ->task_id] ->name }}</td>
                                <td>{{ $selectList ->grade }}</td>
                                <td>{{ $arrMajorsInfo[$selectList ->major_id] ->name }}</td>
                                <td>{{ $arrCoursesInfo[$selectList ->course_id] ->name }}</td>
                                <td>
                                    @if(!empty($selectList ->book_ids))
                                        @foreach($selectList ->book_ids as $bookId)
                                            @if($arrBooksInfo[$bookId] ->type == \App\Models\Book::$bookForStudent)
                                                {{$arrBooksInfo[$bookId] ->name}},
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($selectList ->book_ids))
                                        @foreach($selectList ->book_ids as $bookId)
                                            @if($arrBooksInfo[$bookId] ->type == \App\Models\Book::$bookForTeacher)
                                                {{$arrBooksInfo[$bookId] ->name}}
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ $selectStatusMeaning[$selectList ->status] }}</td>
                                <td class="btn-group-vertical">
                                    @if(in_array($selectList ->status, \App\Models\SelectList::getStatusforEdit()))
                                        <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="popIframeWithCloseFunc('书籍选择', '{{ route('tasksmanage.editselectbooksview', ['selectId' =>$selectList->id]) }}', '675px', '535px', clickXFunc)">书籍选择</a>
                                        <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="popIframeWithCloseFunc('添加书籍', '{{ route('booksmanage.addbookview') }}', '675px', '535px', clickXFunc)">添加书籍</a>
                                        <a href="javascript:void(0);" class="btn btn-success btn-sm" onclick="ajaxASubmit('{{ route('tasksmanage.submitselectlist', ['selectId' =>$selectList ->id]) }}')">提交审核</a>
                                    @endif
                                </td>
                            </tr>
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
