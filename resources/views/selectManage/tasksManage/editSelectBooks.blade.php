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
        <div id="edit-selectbooks-div" class="panel panel-primary" style="width: 100%;">
            <div class="panel-heading">
                <h3>书籍选择</h3>
            </div>
            <div class="panel-body">
                <form id="edit-selectbooks-form" action="{{ route('tasksmanage.saveselectbooks', ['selectId' =>$selectList ->id]) }}" method="post" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label for="books_for_stu">教材类书籍</label>
                        <select name="books_for_stu[]" id="books_for_stu" class="selectpicker show-menu-arrow form-control bs-select-hidden" multiple="multiple" data-live-search="true" title="请选择教材类书籍">
                            @foreach($booksForStu as $book)
                                <option value="{{ $book ->id }}" @if(!empty($selectList ->book_ids) && in_array($book ->id, json_decode($selectList ->book_ids, true))) selected @endif >{{ $book ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="books_for_tea">教参类书籍</label>
                        <select name="books_for_tea[]" id="books_for_tea" class="selectpicker show-menu-arrow form-control bs-select-hidden" multiple="multiple" data-live-search="true" title="请选择教材类书籍">
                            @foreach($booksForTea as $book)
                                <option value="{{ $book ->id }}" @if(!empty($selectList ->book_ids) && in_array($book ->id, json_decode($selectList ->book_ids, true))) selected @endif >{{ $book ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('edit-selectbooks-form')">&nbsp;保&nbsp;存&nbsp;</button>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
