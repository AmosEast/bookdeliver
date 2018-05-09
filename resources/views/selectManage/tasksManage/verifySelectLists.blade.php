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
        {{--当前审核列表 --}}
        <div id="list-selectlist-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>选书审核列表</h3>
            </div>
            <div class="panel-body table-responsive">
                <div class="panel">
                    <div class="panel-body">
                        <form id="query-selectlists-form" method="post" action="{{ route('tasksmanage.verifyselectlistsview') }}" class="form-inline">
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
                                <label class="sr-only" for="select_academy">学院选择</label>
                                <select name="select_academy" id="select_academy" class="form-control" title="请选择学院">
                                    <option value="0">请选择学院</option>
                                    @foreach($arrAcademiesInfo as $academy)
                                        <option value="{{ $academy ->id }}" @if(old('select_academy') == $academy ->id)selected @endif>{{ $academy ->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span>-</span>
                            <div class="form-group">
                                <label class="sr-only" for="select_status">状态选择</label>
                                <select name="select_status" id="select_status" class="form-control" title="请选择状态">
                                    <option value="-1">请选择状态</option>
                                    @foreach($selectStatusMeaning as $status =>$meaning)
                                        <option value="{{ $status }}" @if(!is_null(old('select_status')) && old('select_status') == $status)selected @endif>{{ $meaning }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span>-</span>
                            <div class="form-group">
                                <label class="sr-only" for="select_book">书籍选择</label>
                                <select name="select_book" id="select_book" class="selectpicker form-control" data-size="6" title="请选择书籍" data-live-search="true">
                                    <option value="0">请选择书籍</option>
                                    @foreach($arrBooksInfo as $book)
                                        <option value="{{ $book ->id }}" @if(old('select_book') == $book ->id)selected @endif >{{ $book ->name }}</option>
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
                            <form id="select_lists_batch" action="" method="post" onsubmit="return false;">
                                @csrf
                                <tr>
                                    <th><input id="select_control" type="checkbox" onclick="selectAll('select_control', 'select_ids[]')"></th><th>#</th><th>任务名称</th><th>年级</th><th>学院名称</th><th>专业名称</th><th>课程名称</th><th>教材类书籍</th><th>教参类书籍</th><th>选书者</th><th>审核状态</th>
                                </tr>
                                @if(!empty($selectLists))
                                    @foreach($selectLists as $selectList)
                                        <tr>
                                            <td><input type="checkbox" name="select_ids[]" value="{{ $selectList ->id }}" /> </td>
                                            <td>{{ $selectList ->id }}</td>
                                            <td>{{ $arrTasksInfo[$selectList ->task_id] ->name }}</td>
                                            <td>{{ $selectList ->grade }}</td>
                                            <td>{{ $arrAcademiesInfo[$selectList ->academy_id] ->name }}</td>
                                            <td>{{ $arrMajorsInfo[$selectList ->major_id] ->name }}</td>
                                            <td>{{ $arrCoursesInfo[$selectList ->course_id] ->name }}</td>
                                            <td>
                                                @if(!empty($selectList ->book_ids))
                                                    @foreach($selectList ->book_ids as $bookId)
                                                        @if($arrBooksInfo[$bookId] ->type == \App\Models\Book::$bookForStudent)
                                                            <a href="javascript:void(0);" onclick="popIframe('书籍信息' ,'{{ route('booksmanage.getbookinfo', ['bookId' =>$bookId]) }}', '675px', '515px')">
                                                                {{$arrBooksInfo[$bookId] ->name}},
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($selectList ->book_ids))
                                                    @foreach($selectList ->book_ids as $bookId)
                                                        @if($arrBooksInfo[$bookId] ->type == \App\Models\Book::$bookForTeacher)
                                                            <a href="javascript:void(0);" onclick="popIframe('书籍信息' ,'{{ route('booksmanage.getbookinfo', ['bookId' =>$bookId]) }}', '675px', '515px')">
                                                                {{$arrBooksInfo[$bookId] ->name}},
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{{ $arrUsersInfo[$selectList ->selector_id] ->name}}</td>
                                            <td>{{ $selectStatusMeaning[$selectList ->status] }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td colspan="10">
                                        <div class="btn-group">
                                            <input type="submit" class="btn btn-success" value="批量通过" onclick="changeFormActionBeforeSubmit('select_lists_batch', '{{ route('tasksmanage.batchchangeselectstatus', ['selectStatus' =>\App\Models\SelectList::$finalAgreed]) }}')">
                                            <input type="submit" class="btn btn-primary" value="批量重置" onclick="changeFormActionBeforeSubmit('select_lists_batch', '{{ route('tasksmanage.batchchangeselectstatus', ['selectStatus' =>\App\Models\SelectList::$noSubmit]) }}')">
                                            <input type="submit" class="btn btn-danger" value="批量拒绝" onclick="changeFormActionBeforeSubmit('select_lists_batch', '{{ route('tasksmanage.batchchangeselectstatus', ['selectStatus' =>\App\Models\SelectList::$finalRefused]) }}')" >
                                        </div>
                                    </td>
                                </tr>
                            </form>
                        </table>
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
