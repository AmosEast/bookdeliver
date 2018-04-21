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
        <div id="edit-major-div" class="panel panel-primary" style="width: 100%;">
            <div class="panel-heading">
                <h3>编辑专业</h3>
            </div>
            <div class="panel-body">
                <form id="edit-major-form" action="{{ route('majorsmanage.updatemajorinfo', ['majorId' =>$major ->id]) }}" method="post" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label for="major_unique_id">专业编号</label>
                        <input type="text" class="form-control" id="major_unique_id" name="major_unique_id" value="{{ $major ->unique_id }}">
                    </div>
                    <div class="form-group">
                        <label for="major_name">专业名称</label>
                        <input type="text" class="form-control" id="major_name" name="major_name" value="{{ $major ->name }}">
                    </div>
                    <div class="form-group">
                        <label for="major_description">专业描述</label>
                        <input type="text" class="form-control" id="major_description" name="major_description" value="{{ $major ->description }}">
                    </div>
                    <div class="form-group">
                        <label for="major_academy_id">所属学院</label>
                        <select name="major_academy_id" id="major_academy_id" class="form-control">
                            @foreach($academies as $academy)
                                <option value="{{ $academy ->id }}" @if($academy ->id == $major ->academy_id) selected @endif>{{ $academy ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group has-warning">
                        <label for="major_valid">专业状态</label>
                        <select class="form-control" id="major_valid" name="major_valid">
                            @if($major ->is_valid == 1)
                                <option value="1" selected>启用</option>
                                <option value="0">弃用</option>
                            @else
                                <option value="1">启用</option>
                                <option value="0" selected>弃用</option>
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" onclick="ajaxFormSubmit('edit-major-form')">更改</button>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
