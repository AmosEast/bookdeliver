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
        <div id="edit-permission-div" class="panel panel-primary" style="width: 100%;">
            <div class="panel-heading">
                <h3>编辑权限</h3>
            </div>
            <div class="panel-body">
                <form id="edit-permission-form" action="{{ route('permissionsmanage.updatepermissioninfo', ['permissionId' =>$permission ->id]) }}" method="post" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label for="permission_name">权限名称</label>
                        <input type="text" class="form-control" id="permission_name" name="permission_name" value="{{ $permission ->name }}">
                    </div>
                    <div class="form-group">
                        <label for="permission_description">权限描述</label>
                        <input type="text" class="form-control" id="permission_description" name="permission_description" value="{{ $permission ->description }}">
                    </div>
                    <div class="form-group">
                        <label for="permission_controller">权限Controller</label>
                        <input type="text" class="form-control" id="permission_controller" name="permission_controller" value="{{ $permission ->controller }}">
                    </div>
                    <div class="form-group">
                        <label for="permission_function">权限Function</label>
                        <input type="text" class="form-control" id="permission_function" name="permission_function" value="{{ $permission ->function }}">
                    </div>
                    <div class="form-group">
                        <label for="permission_valid">权限状态</label>
                        <select class="form-control" id="permission_valid" name="permission_valid">
                            @if($permission ->is_valid == 1)
                                <option value="1" selected>启用</option>
                                <option value="0">弃用</option>
                            @else
                                <option value="1">启用</option>
                                <option value="0" selected>弃用</option>
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" onclick="ajaxFormSubmit('edit-permission-form')">更改</button>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
