@extends('layouts.baseFrame')
@section('content')
    @if($error == 1)
        <div id="error-info-div" class="panel panel-danger" style="width: 100%;">
            <div class="panel-heading">
                <h3>错误信息提示</h3>
            </div>
            <div class="panel-body" style="text-align: center;">
                <h4>{{ $msg }}</h4>
            </div>
        </div>
    @else
        <div id="role-permissions-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>角色权限列表</h3>
            </div>
            <div class="panel-body table-responsive">
                <table id="role-permissions-table" class="table table-striped table-hover">
                    <tr>
                        <th>#</th><th>权限名称</th><th>权限描述</th><th>授权时间</th><th>操作</th>
                    </tr>
                    @if(!empty($rolePermissions))
                        @foreach($rolePermissions as $permission)
                            <tr>
                                <td>{{ $permission ->pivot ->permission_id }}</td>
                                <td>{{ $permission ->permission_name }}</td>
                                <td>{{ $permission ->permission_description }}</td>
                                <td>{{ $permission ->pivot ->updated_at }}</td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-success btn-sm" onclick="ajaxASubmit('{{ route('rolesmanage.removepermission', ['roleId' =>$permission ->pivot ->role_id, 'permissionId' =>$permission ->pivot ->permission_id]) }}')">取消授权</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </div>

        <div id="not-permissions-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>授权列表</h3>
            </div>
            <div class="panel-body table-responsive">
                <table id="not-permissions-table" class="table table-striped table-hover">
                    <form id="not-permission-form" method="post" action="{{ route('rolesmanage.givepermission', ['roleId' =>$roleId]) }}" onsubmit="return false;">
                        @csrf
                        <tr>
                            <th>选择</th><th>#</th><th>权限名称</th><th>权限描述</th><th>更新时间</th>
                        </tr>
                        @if(!empty($notPermissions))
                            @foreach($notPermissions as $permission)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="arr_permission_gived[]" value="{{ $permission ->id }}">
                                    </td>
                                    <td>{{ $permission ->id }}</td>
                                    <td>{{ $permission ->name }}</td>
                                    <td>{{ $permission ->description }}</td>
                                    <td>{{ $permission ->updated_at }}</td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td colspan="5" style="text-align: right;">
                                <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('not-permission-form')">&nbsp;授&nbsp;权&nbsp;</button>
                            </td>
                        </tr>
                    </form>
                </table>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
