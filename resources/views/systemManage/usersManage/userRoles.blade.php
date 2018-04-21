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
        <div id="user-roles-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>用户角色列表</h3>
            </div>
            <div class="panel-body table-responsive">
                <table id="user-roles-table" class="table table-striped table-hover">
                    <tr>
                        <th>#</th><th>角色名称</th><th>角色描述</th><th>授权时间</th><th>操作</th>
                    </tr>
                    @if(!empty($userRoles))
                        @foreach($userRoles as $role)
                            <tr>
                                <td>{{ $role ->pivot ->role_id }}</td>
                                <td>{{ $role ->name }}</td>
                                <td>{{ $role ->description }}</td>
                                <td>{{ $role ->pivot ->updated_at }}</td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-success brn-xs" onclick="ajaxASubmit('{{ route('usersmanage.removerole', ['userId' =>$role ->pivot ->user_id, 'roleId' =>$role ->pivot ->role_id]) }}')">移除角色</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </div>

        <div id="not-roles-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>未分配角色列表</h3>
            </div>
            <div class="panel-body table-responsive">
                <table id="not-roles-table" class="table table-striped table-hover">
                    <form id="not-roles-form" method="post" action="{{ route('usersmanage.giverole', ['userId' =>$userId]) }}" onsubmit="return false;">
                        @csrf
                        <tr>
                            <th>选择</th><th>#</th><th>角色名称</th><th>角色描述</th><th>更新时间</th>
                        </tr>
                        @if(!empty($notRoles))
                            @foreach($notRoles as $role)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="arr_role_gived[]" value="{{ $role ->id }}">
                                    </td>
                                    <td>{{ $role ->id }}</td>
                                    <td>{{ $role ->name }}</td>
                                    <td>{{ $role ->description }}</td>
                                    <td>{{ $role ->updated_at }}</td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td colspan="5" style="text-align: right;">
                                <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('not-roles-form')">&nbsp;授&nbsp;权&nbsp;</button>
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
