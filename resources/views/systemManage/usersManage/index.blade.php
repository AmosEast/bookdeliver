@extends('layouts.baseFrame')
@section('content')
    <div id="add-user-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加用户</h3>
        </div>
        <div class="panel-body">
            <div class="btn-group">
                <a href="javascript:void(0)" class="btn btn-success" onclick="popIframeWithCloseFunc('添加单个用户', '{{ route('usersmanage.addauserview') }}', '675px', '535px', clickXFunc)">添加单个用户</a>
                <a href="javascript:void(0)" class="btn btn-info" onclick="popIframeWithCloseFunc('批量导入用户', '{{ route('usersmanage.addmanyusersview') }}', '675px', '535px', clickXFunc)">批量导入用户</a>
                <a href="{{ route('usersmanage.downloadexcelexample') }}" class="btn btn-link" >查看批量导入用户名单excel样例</a>
            </div>
        </div>
    </div>
    <div id="list-user-div" class="panel panel-success" style="width: 100%;">
        <div class="panel-heading">
            <h3>用户列表</h3>
        </div>
        <div class="panel-body table-responsive">
            <table id="list-user-table" class="table table-striped table-hover">
                <tr>
                    <th>#</th><th>编号</th><th>用户名</th><th>邮箱</th><th>手机号</th><th>状态</th><th>更新时间</th><th>操作</th><th>角色控制</th>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <th>{{ $user ->id }}</th>
                        <th>{{ $user ->unique_id }}</th>
                        <th>{{ $user ->name }}</th>
                        <th>{{ $user ->email }}</th>
                        <th>{{ $user ->mobile }}</th>
                        <th>{{ $user ->is_valid ? '可用':'禁用' }}</th>
                        <th>{{ $user ->updated_at }}</th>
                        <th>
                            <a href="javascript:void(0);" class="btn btn-success btn-sm" onclick="">详细信息</a>
                            <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="">修改信息</a>
                            <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="confirmResetPassword('{{ $user ->name }}', '{{ route('usersmanage.resetpassword', ['userId' =>$user ->id]) }}')">重置密码</a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="popIframe('角色控制', '{{ route('usersmanage.userroles', ['userId' =>$user ->id]) }}', '1015px', '535px')">角色控制</a>
                        </th>

                    </tr>
                @endforeach
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

        function confirmResetPassword(userName, resetUrl) {
            layer.confirm('您确定重置用户 ' + userName +' 的密码吗？', {icon: 3, title:'重置密码确认'}, function (index) {
                layer.close(index);
                ajaxASubmit(resetUrl);
            });
        }
    </script>
@endsection
