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
                        <form id="query-selectlists-form" method="get" action="{{ route('studentorder.index') }}" class="form-inline">
                            @csrf
                            <div class="form-group">
                                <label class="sr-only" for="query_task">任务选择</label>
                                <select name="query_task" id="query_task" class="form-control" title="请选择任务">
                                    <option value="0">请选择任务</option>
                                    @foreach($arrTasksInfo as $task)
                                        <option value="{{ $task ->id }}" @if(old('query_task') == $task ->id)selected @endif>{{ $task ->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span>-</span>
                            <div class="form-group">
                                <label class="sr-only" for="query_status">购书状态选择</label>
                                <select name="query_status" id="query_status" class="form-control" title="请选择购书状态">
                                    <option value="0">请选择购书状态</option>
                                    <option value="1" @if(old('query_status') == 1) selected @endif>已购书籍</option>
                                    <option value="-1" @if(old('query_status') == -1) selected @endif>未购书籍</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">&nbsp;搜&nbsp;索&nbsp;</button>
                        </form>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-body table-responsive">
                        <div>
                            <strong>学院：</strong>{{ $academy ->name }}&nbsp;&nbsp;&nbsp;
                            <strong>专业：</strong>{{ $major ->name }}&nbsp;&nbsp;&nbsp;
                            <strong>年级：</strong>{{ $schoolClass ->grade }}&nbsp;&nbsp;&nbsp;
                        </div>
                        <br>
                        <table id="list-selectlists-table" class="table table-center table-border-my">
                            <form id="order_books_form" action="{{ route('studentorder.orderbooks') }}" method="post" onsubmit="return false;">
                                @csrf
                                <tr>
                                    <th rowspan="2">#</th><th rowspan="2">任务名称</th><th rowspan="2">课程名称</th><th rowspan="2">已购书籍</th><th colspan="4">书籍订购</th>
                                </tr>
                                <tr>
                                    <th>名称</th><th>定价</th><th>折扣</th><th>订购数量</th>
                                </tr>
                                @if(!empty($selectLists))
                                    @foreach($selectLists as $selectList)
                                        <tr>
                                            <td rowspan="{{ count($selectList ->book_ids) > 0 ? count($selectList ->book_ids) + 1 : 2 }}">{{ $selectList ->id }}</td>
                                            <td rowspan="{{ count($selectList ->book_ids) > 0 ? count($selectList ->book_ids) + 1 : 2 }}">{{ $arrTasksInfo[$selectList ->task_id] ->name }}</td>
                                            <td rowspan="{{ count($selectList ->book_ids) > 0 ? count($selectList ->book_ids) + 1 : 2 }}">{{ $selectList ->course ->name }}</td>
                                            <td rowspan="{{ count($selectList ->book_ids) > 0 ? count($selectList ->book_ids) + 1 : 2 }}">
                                                @if(isset($arrOrdersInfo[$selectList ->id]) && !empty($arrOrdersInfo[$selectList ->id]))
                                                    @foreach($arrOrdersInfo[$selectList ->id] as $order)
                                                        {{ $arrBooksInfo[$order ->book_id] ->name }}X{{ $order ->quantity }}<br />
                                                    @endforeach
                                                @endif
                                            </td>
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
                                                    <td class="input-group">
                                                        <input name="stu_orders[{{ $book_id }}][task_id]" type="hidden" value="{{ $selectList ->task_id }}">
                                                        <input name="stu_orders[{{ $book_id }}][select_id]" type="hidden" value="{{ $selectList ->id }}">
                                                        <input name="stu_orders[{{ $book_id }}][quantity]" class="form-control" type="text" value="0">
                                                        <span class="input-group-addon">本</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    @endforeach
                                @endif
                                <tr>
                                    <td colspan="8" style="text-align: right !important;">当前需付：<span id="curConsume" style="font-weight: bold;">{{ number_format($curConsume, 2) }}</span>元</td>
                                </tr>
                                <tr>
                                    <td colspan="8"><a class="btn btn-primary btn-sm" href="javascript:void(0)" onclick="ajaxFormSubmit('order_books_form')">书籍订购</a></td>
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
