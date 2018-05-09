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
        <div id="book-div" class="panel panel-primary" style="width: 100%;">
            <div class="panel-heading">
                <h3>书籍信息</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <th>ISBN</th><td>{{ $book ->isbn }}</td>
                    </tr>
                    <tr>
                        <th>名称</th><td>{{ $book ->name }}</td>
                    </tr>
                    <tr>
                        <th>作者</th><td>{{ $book ->author }}</td>
                    </tr>
                    <tr>
                        <th>出版社</th><td>{{ $book ->publishing }}</td>
                    </tr>
                    <tr>
                        <th>类型</th><td>{{ $typeMeaning[$book ->type] }}</td>
                    </tr>
                    <tr>
                        <th>定价</th><td>{{ $book ->price }} 元</td>
                    </tr>
                    <tr>
                        <th>简介</th><td>{{ $book ->description }}</td>
                    </tr>
                    <tr>
                        <th>上传者</th><td>{{ $book ->creator ->name }}</td>
                    </tr>
                    <tr>
                        <th>更新者</th><td>{{ $book ->updater ->name }}</td>
                    </tr>
                    <tr>
                        <th>更新时间</th><td>{{ $book ->updated_at }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
