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
                <div>
                    <strong>任务：</strong>{{ $task ->name }}&nbsp;&nbsp;&nbsp;
                    <strong>学院：</strong>{{ $academy ->name }}&nbsp;&nbsp;&nbsp;
                    <strong>专业：</strong>{{ $major ->name }}&nbsp;&nbsp;&nbsp;
                    <strong>年级：</strong>{{ $schoolClass ->grade }}&nbsp;&nbsp;&nbsp;
                    <strong>班级：</strong>{{ $schoolClass ->name }}&nbsp;&nbsp;&nbsp;
                </div>
                <br>
                <table id="list-selectlists-table" class="table table-center table-border-my">
                    <form id="order_books_form" action="{{ route('classorder.orderbooks') }}" method="post" onsubmit="return false;">
                        @csrf
                        <input name="task_id" type="hidden" value="{{ $task ->id }}">
                        <input name="class_id" type="hidden" value="{{ $schoolClass ->id }}">
                        <tr>
                            <th rowspan="2">#</th><th rowspan="2">课程名称</th><th colspan="5">书籍订购</th>
                        </tr>
                        <tr>
                            <th>名称</th><th>定价</th><th>折扣</th><th>班级订购数量</th><th><input id="select_all_books" type="checkbox" onclick="selectAll('select_all_books', 'books_selected[]')"></th>
                        </tr>
                        @if(!empty($selectLists))
                            @foreach($selectLists as $selectList)
                                <tr>
                                    <td rowspan="{{ count($selectList ->book_ids) > 0 ? count($selectList ->book_ids) + 1 : 2 }}">{{ $selectList ->id }}</td>
                                    <td rowspan="{{ count($selectList ->book_ids) > 0 ? count($selectList ->book_ids) + 1 : 2 }}">{{ $selectList ->course ->name }}</td>
                                </tr>
                                @if(empty($selectList ->book_ids))
                                    <tr>
                                        <td colspan="2">无可用订购</td>
                                    </tr>
                                @else
                                    @foreach($selectList ->book_ids as $book_id)
                                        <tr>
                                            <td><a href="javascript:void(0);" onclick="popIframe('书籍信息', '{{ route('booksmanage.getbookinfo', ['bookId' =>$book_id]) }}', '675px', '515px')">{{ $arrBooksInfo[$book_id] ->name }}</a></td>
                                            <td>{{ number_format($arrBooksInfo[$book_id] ->price, 2) }}元</td>
                                            <td>{{ number_format($arrBooksInfo[$book_id] ->discount, 1) }}折</td>
                                            <td>
                                                <span style="font-weight: bold;">{{ $studentsNum }}</span>
                                                <span>本</span>
                                            </td>
                                            <td><input name="books_selected[]" type="checkbox" value="{{ $book_id }}"></td>
                                        </tr>
                                    @endforeach
                                @endif

                            @endforeach
                        @endif
                        <tr>
                            <td colspan="9"><a class="btn btn-primary btn-sm" href="javascript:void(0)" onclick="ajaxFormSubmitWithCallback('order_books_form', redirectFunc, redirectFunc)">书籍订购</a></td>
                        </tr>
                    </form>
                </table>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
    <script type="text/javascript">
        {{-- 弹窗关闭按钮点击时的函数回调 --}}
        function redirectFunc() {
            window.location = '{{ route('classorder.index') }}';
        }
        function changeFormActionBeforeSubmit(formId ,url) {
            var form = $('#' + formId);
            form.attr('action', url);
            ajaxFormSubmit(formId);
        }
    </script>
@endsection
