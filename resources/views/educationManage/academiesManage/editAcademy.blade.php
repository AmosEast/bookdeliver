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
        <div id="edit-academy-div" class="panel panel-primary" style="width: 100%;">
            <div class="panel-heading">
                <h3>编辑学院</h3>
            </div>
            <div class="panel-body">
                <form id="edit-academy-form" action="{{ route('academiesmanage.updateacademyinfo', ['academyId' =>$academy ->id]) }}" method="post" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label for="academy_unique_id">学院编号</label>
                        <input type="text" class="form-control" id="academy_unique_id" name="academy_unique_id" value="{{ $academy ->unique_id }}">
                    </div>
                    <div class="form-group">
                        <label for="academy_name">学院名称</label>
                        <input type="text" class="form-control" id="academy_name" name="academy_name" value="{{ $academy ->name }}">
                    </div>
                    <div class="form-group">
                        <label for="academy_description">学院描述</label>
                        <input type="text" class="form-control" id="academy_description" name="academy_description" value="{{ $academy ->description }}">
                    </div>
                    <div class="form-group has-warning">
                        <label for="academy_valid">学院状态</label>
                        <select class="form-control" id="academy_valid" name="academy_valid">
                            @if($academy ->is_valid == 1)
                                <option value="1" selected>启用</option>
                                <option value="0">弃用</option>
                            @else
                                <option value="1">启用</option>
                                <option value="0" selected>弃用</option>
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" onclick="ajaxFormSubmit('edit-academy-form')">更改</button>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
