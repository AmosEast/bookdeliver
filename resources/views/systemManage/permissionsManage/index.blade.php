@extends('layouts.baseFrame')
@section('content')
    <div id="add-permission-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加权限</h3>
        </div>
        <div class="panel-body">
            <form id="add-permission-form" method="post" action="{{ route('permissionsmanage.addpermission') }}" class="form-inline" onsubmit="return false;">
                @csrf
                <div class="form-group">
                    <label class="sr-only" for="permission-name">权限名称</label>
                    <input type="text" class="form-control" id="permission-name" name="permission_name" placeholder="请输入权限名">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="permission-description">权限简介</label>
                    <input type="text" class="form-control" id="permission-name" name="permission_description" placeholder="请输入权限简介">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="permission-controller">controller</label>
                    <input type="text" class="form-control" id="permission-controller" name="permission_controller" placeholder="请输入controller">
                </div>
                <span>-</span>
                <div class="form-group">
                    <label class="sr-only" for="permission-function">function</label>
                    <input type="text" class="form-control" id="permission-function" name="permission_function" placeholder="请输入function">
                </div>
                <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('add-permission-form')">&nbsp;添&nbsp;加&nbsp;</button>
            </form>
        </div>
    </div>
    <div id="list-permission-div" class="panel panel-success" style="width: 100%;">
        <div class="panel-heading">
            <h3>权限列表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table id="list-permission-table" class="table table-striped table-hover">
                <tr>
                    <th>#</th><th>权限名</th><th>权限描述</th><th>状态</th><th>创建时间</th><th>更新时间</th><th>更新人</th><th>操作</th>
                </tr>
                @if(!empty($permissions))
                    @foreach($permissions as $permission)
                        <tr>
                            <td>{{ $permission ->id }}</td>
                            <td>{{ $permission ->name }}</td>
                            <td>{{ $permission ->description }}</td>
                            <td>{{ $permission ->is_valid == 1 ? '启用':'弃用' }}</td>
                            <td>{{ $permission ->created_at }}</td>
                            <td>{{ $permission ->updated_at }}</td>
                            <td>{{ $permission ->updater }}</td>
                            <td><a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="popIframeWithCloseFunc('编辑权限', '{{ route('permissionsmanage.editpermissionview', ['permissionId' =>$permission ->id]) }}', '675px', '535px', clickXFunc)">编辑</a></td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
@endsection

@section('js-text-part')
    <script type="text/javascript">
        {{-- 弹窗关闭按钮点击时的函数回调 --}}
        function clickXFunc() {
            window.location.reload();
        }
    </script>
@endsection
