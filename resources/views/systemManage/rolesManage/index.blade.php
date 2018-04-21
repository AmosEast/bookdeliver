@extends('layouts.baseFrame')
@section('content')
    <div id="add-role-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加角色</h3>
        </div>
        <div class="panel-body">
            <form id="add-role-form" method="post" action="{{ route('rolesmanage.addrole') }}" class="form-inline" onsubmit="return false;">
                @csrf
                <div class="form-group">
                    <label class="sr-only" for="role-name">角色名</label>
                    <input type="text" class="form-control" id="role-name" name="role_name" placeholder="请输入角色名">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="role-description">角色简介</label>
                    <input type="text" class="form-control" id="role-description" placeholder="请输入角色简介" name="role_description">
                </div>
                <div class="form-group">
                    <label for="role-level" class="sr-only">角色类型</label>
                    <select id="role-level" name="role_level" class="form-control">
                        @foreach($roleLevels as $level =>$desc)
                            <option value="{{ $level }}">{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('add-role-form')">&nbsp;添&nbsp;加&nbsp;</button>
            </form>
        </div>
    </div>
    <div id="list-role-div" class="panel panel-success" style="width: 100%;">
        <div class="panel-heading">
            <h3>角色列表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table id="list-role-table" class="table table-striped table-hover">
                <tr>
                    <th>#</th><th>角色名</th><th>角色描述</th><th>角色类型</th><th>创建时间</th><th>更新时间</th><th>更新人</th><th>操作</th><th>权限控制</th>
                </tr>
                @if(!empty($roles))
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ $role ->id }}</td>
                            <td>{{ $role ->name }}</td>
                            <td>{{ $role ->description }}</td>
                            <td>{{ $roleLevels[$role ->level] }}</td>
                            <td>{{ $role ->created_at }}</td>
                            <td>{{ $role ->updated_at }}</td>
                            <td>{{ $role ->updater }}</td>
                            <td>
                                @if($role ->is_valid == 1)
                                    <a href="javascript:void(0);" class="btn btn-danger brn-xs" onclick="ajaxASubmit('{{ route('rolesmanage.disablerole', ['id' =>$role ->id]) }}')">弃用</a>
                                @else
                                    <a href="javascript:void(0);" class="btn btn-success brn-xs" onclick="ajaxASubmit('{{ route('rolesmanage.startrole', ['id' =>$role ->id]) }}')">启用</a>
                                @endif
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-success brn-xs" onclick="popIframe('权限控制', '{{ route('rolesmanage.rolepermissions', ['id' =>$role ->id]) }}', '1015px', '535px')">权限控制</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
@endsection

@section('js-text-part')
@endsection