@extends('layouts.baseFrame')
@section('content')
    <div id="add-role-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加角色</h3>
        </div>
        <div class="panel-body">
            <form id="add-role-form" method="post" action="#" class="form-inline">
                <div class="form-group">
                    <label class="sr-only" for="role-name">角色名</label>
                    <input type="text" class="form-control" id="role-name" name="role_name" placeholder="请输入角色名">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="role-description">角色简介</label>
                    <input type="text" class="form-control" id="role-description" placeholder="请输入角色简介" name="role_description">
                </div>
                <button type="button" class="btn btn-primary">&nbsp;添&nbsp;加&nbsp;</button>
            </form>
        </div>
    </div>
@endsection