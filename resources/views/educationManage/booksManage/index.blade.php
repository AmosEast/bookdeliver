@extends('layouts.baseFrame')
@section('content')
    <div id="add-books-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加书籍</h3>
        </div>
        <div class="panel-body">
            <a href="javascript:void(0);" class="btn btn-primary brn-xs" onclick="popIframeWithCloseFunc('添加用户', '{{ route('booksmanage.addbookview') }}', '675px', '535px', clickXFunc)">添加书籍</a>
        </div>
    </div>
    <div id="list-book-div" class="panel panel-success" style="width: 100%;">
        <div class="panel-heading">
            <h3>书籍列表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table id="list-books-table" class="table table-striped table-hover">
                <tr>
                    <th>#</th><th>ISBN</th><th>书籍名</th><th>作者</th><th>书籍描述</th><th>出版社</th><th>所属课程</th><th>类型</th><th>价格</th><th>折扣</th><th>状态</th><th>更新时间</th><th>更新人</th><th>操作</th>
                </tr>
                @if(!empty($books))
                    @foreach($books as $book)
                        <tr>
                            <td>{{ $book ->id }}</td>
                            <th>{{ $book ->isbn }}</th>
                            <td>{{ $book ->name }}</td>
                            <td>{{ $book ->author }}</td>
                            <td>{{ $book ->description }}</td>
                            <td>{{ $book ->publishing }}</td>
                            <td>{{ $book ->course ->name }}</td>
                            <td>{{ $bookTypes[$book ->type] }}</td>
                            <td>{{ number_format($book ->price, 2) }}元</td>
                            <td>{{ number_format($book ->discount, 1) }}折</td>
                            <td>{{ $book ->is_valid == 1 ? '启用':'弃用' }}</td>
                            <td>{{ $book ->updated_at }}</td>
                            <td>{{ $book ->updater ->name }}</td>
                            <td><a href="javascript:void(0);" class="btn btn-primary brn-xs" onclick="popIframeWithCloseFunc('编辑书籍', '{{ route('booksmanage.editbookview', ['bookId' =>$book ->id]) }}', '675px', '535px', clickXFunc)">编辑</a></td>
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
