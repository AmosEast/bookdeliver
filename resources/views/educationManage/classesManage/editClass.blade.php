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
        <div id="edit-class-div" class="panel panel-primary" style="width: 100%;">
            <div class="panel-heading">
                <h3>编辑班级</h3>
            </div>
            <div class="panel-body">
                <form id="edit-class-form" action="{{ route('classesmanage.updateclassinfo', ['classId' =>$class ->id]) }}" method="post" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label for="class_unique_id">班级编号</label>
                        <input type="text" class="form-control" id="class_unique_id" name="class_unique_id" value="{{ $class ->unique_id }}">
                    </div>
                    <div class="form-group">
                        <label for="class_name">班级名称</label>
                        <input type="text" class="form-control" id="class_name" name="class_name" value="{{ $class ->name }}">
                    </div>
                    <div class="form-group">
                        <label for="class_description">班级描述</label>
                        <input type="text" class="form-control" id="class_description" name="class_description" value="{{ $class ->description }}">
                    </div>
                    <div class="form-group">
                        <label for="class_major_id">所属专业</label>
                        <select name="class_major_id" id="class_major_id" class="form-control">
                            @foreach($majors as $major)
                                <option value="{{ $major ->id }}" @if($major ->id == $class ->major_id) selected @endif>{{ $major ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="class_grade">年级</label>
                        <select name="class_grade" id="class_grade" class="form-control">
                            @foreach($grades as $grade)
                                <option value="{{ $grade }}" @if($class ->grade == $grade) selected @endif>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group has-warning">
                        <label for="class_valid">班级状态</label>
                        <select class="form-control" id="class_valid" name="class_valid">
                            @if($class ->is_valid == 1)
                                <option value="1" selected>启用</option>
                                <option value="0">弃用</option>
                            @else
                                <option value="1">启用</option>
                                <option value="0" selected>弃用</option>
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" onclick="ajaxFormSubmit('edit-class-form')">更改</button>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
