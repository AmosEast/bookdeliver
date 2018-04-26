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
                <h3>{{ $formTitle }}</h3>
            </div>
            <div class="panel-body">
                <form id="book-form" action="{{ $formSubmitUrl }}" method="post" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label for="book_isbn">ISBN</label>
                        <input type="text" class="form-control" id="book_isbn" name="book_isbn" placeholder="请输入书籍ISBN号" @if(isset($book)) value="{{ $book ->isbn }}" @endif>
                    </div>
                    <div class="form-group">
                        <label for="book_name">书籍名称</label>
                        <input type="text" class="form-control" id="book_name" name="book_name" placeholder="请输入书籍名称" @if(isset($book)) value="{{ $book ->name }}" @endif>
                    </div>
                    <div class="form-group">
                        <label for="book_author">书籍作者</label>
                        <input type="text" class="form-control" id="book_author" name="book_author" placeholder="请输入书籍作者" @if(isset($book)) value="{{ $book ->author }}" @endif>
                    </div>
                    <div class="form-group">
                        <label for="book_description">书籍简介</label>
                        <input type="text" class="form-control" id="book_description" name="book_description" placeholder="请输入书籍简介" @if(isset($book)) value="{{ $book ->description }}" @endif>
                    </div>
                    <div class="form-group">
                        <label for="book_publishing">出版社</label>
                        <input type="text" class="form-control" id="book_publishing" name="book_publishing" placeholder="请输入书籍出版社" @if(isset($book)) value="{{ $book ->publishing }}" @endif>
                    </div>
                    <div class="form-group">
                        <label for="book_price">价格</label>
                        <div class="input-group">
                            <span class="input-group-addon">￥</span>
                            <input type="text" class="form-control" id="book_price" name="book_price" placeholder="请输入书籍价格,保留小数点后两位" @if(isset($book)) value="{{ number_format($book ->price, 2) }}" @endif>
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="book_discount">折扣</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="book_discount" name="book_discount" placeholder="请输入书籍折扣" value="@if(isset($book)){{ number_format($book ->discount, 1) }}@else{{ number_format(10.0, 1) }}@endif" >
                            <span class="input-group-addon">折</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="book_type">书籍类型</label>
                        <select name="book_type" id="book_type" class="form-control">
                            @foreach($bookTypes as $type =>$mean)
                                <option value="{{ $type }}" @if(isset($book) && $book ->type == $type) selected @endif>{{ $mean }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="book_course">相关课程</label>
                        <select name="book_course" id="book_course" class="form-control selectpicker " data-live-search="true">
                            @foreach($courses as $course)
                                <option value="{{ $course ->id }}" @if(isset($book) && $book ->course_id == $course ->id) selected @endif>{{ $course ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group has-warning">
                        <label for="book_valid">书籍状态</label>
                        <select class="form-control" id="book_valid" name="book_valid">
                            @if(isset($book) && $book ->is_valid == 0)
                                <option value="1">启用</option>
                                <option value="0" selected>弃用</option>
                            @else
                                <option value="1" selected>启用</option>
                                <option value="0">弃用</option>
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" onclick="ajaxFormSubmit('book-form')">提交</button>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
