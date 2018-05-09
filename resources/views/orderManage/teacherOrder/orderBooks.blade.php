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
        <div id="list-booklist-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>可选书籍列表</h3>
            </div>
            <div class="panel-body table-responsive">
                <table class="table table-bordered table-hover">
                    <form id="booklist" action="{{ route('teacherorder.orderbooks', ['selectId' =>$selectList ->id, 'taskId' =>$selectList ->task_id]) }}" method="post" onsubmit="return false;">
                        @csrf
                        <tr>
                            <th colspan="8" style="text-align: center;">教材类书籍</th>
                        </tr>
                        <tr>
                            <th><input id="books_for_stu" type="checkbox" onclick="selectAll('books_for_stu', 'books_for_stu[]')"></th>
                            <th>订购数量</th>
                            <th>书籍名</th><th>作者</th><th>出版社</th><th>书籍描述</th><th>价格</th><th>折扣</th>
                        </tr>
                        @if(!empty($books))
                            @foreach($books as $book)
                                @if($book ->type == \App\Models\Book::$bookForStudent)
                                    <tr>
                                        <td><input name="books_for_stu[]" type="checkbox" value="{{ $book ->id }}"></td>
                                        <td class="input-group">
                                            <input name="book_quantity[{{ $book ->id }}]" class="form-control" type="text" value="1" placeholder="请输入订购数量">
                                            <span class="input-group-addon">本</span>
                                        </td>
                                        <td>{{ $book ->name }}</td>
                                        <td>{{ $book ->author }}</td>
                                        <td>{{ $book ->publishing }}</td>
                                        <td>{{ $book ->description }}</td>
                                        <td>{{ number_format($book ->price, 2) }}元</td>
                                        <td>{{ number_format($book ->discount, 1) }}折</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        <tr>
                            <th colspan="8" style="text-align: center;">教参类书籍</th>
                        </tr>
                        <tr>
                            <th><input id="books_for_tea" type="checkbox" onclick="selectAll('books_for_tea', 'books_for_tea[]')"></th>
                            <th>订购数量</th><th>书籍名</th><th>作者</th><th>出版社</th><th>书籍描述</th><th>价格</th><th>折扣</th>
                        </tr>
                        @if(!empty($books))
                            @foreach($books as $book)
                                @if($book ->type == \App\Models\Book::$bookForTeacher)
                                    <tr>
                                        <td><input name="books_for_tea[]" type="checkbox" value="{{ $book ->id }}"></td>
                                        <td class="input-group">
                                            <input name="book_quantity[{{ $book ->id }}]" class="form-control" type="text" value="1" placeholder="请输入订购数量">
                                            <span class="input-group-addon">本</span>
                                        </td>
                                        <td>{{ $book ->name }}</td>
                                        <td>{{ $book ->author }}</td>
                                        <td>{{ $book ->publishing }}</td>
                                        <td>{{ $book ->description }}</td>
                                        <td>{{ number_format($book ->price, 2) }}元</td>
                                        <td>{{ number_format($book ->discount, 1) }}折</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        <tr>
                            <td colspan="8">
                                <input class="btn btn-primary" type="submit" value="提交" onclick="ajaxFormSubmit('booklist')">
                            </td>
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
