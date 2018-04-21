@extends('layouts.baseFrame')
@section('content')
    @if($error == 1)
        <div id="error-msg-div" class="panel panel-danger" style="width: 100%;">
            <div class="panel-heading">
                <h3> 错误提示</h3>
            </div>
            <div class="panel-body" style="text-align: center;">
                @foreach($msg as $v)
                    <h4>{{ $v }}</h4>
                @endforeach
            </div>
        </div>
    @else
        <div id="list-user-div" class="panel panel-success" style="width: 100%;">
            <div class="panel-heading">
                <h3>用户列表</h3>
            </div>
            <div class="panel-body table-responsive">
                <table id="list-user-table" class="table table-striped table-hover">
                    <tr>
                        <th>#</th><th>编号</th><th>用户名</th><th>邮箱</th><th>用户手机号</th><th>集体类型</th><th>集体名称</th>
                    </tr>
                    @foreach($users as $key =>$user)
                        <tr>
                            <th>{{ $key }}</th>
                            <th>{{ $user['unique_id'] }}</th>
                            <th>{{ $user['name'] }}</th>
                            <th>{{ $user['email'] }}</th>
                            <th>{{ $user['mobile'] }}</th>
                            <th>{{ $belongTypeMeaning[$user['belong_type']] }}</th>
                            <th>{{ $user['belong_name'] }}</th>
                        </tr>
                    @endforeach
                </table>
                <a href="javascript:void(0)" class="btn btn-primary brn-xs" onclick="ajaxASubmitWithCallback('{{ route('usersmanage.saveusersfromsession') }}', redirectTo, redirectTo)">确认</a>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
    <script type="text/javascript">
        {{-- 点击确认后跳转操作 --}}
        function redirectTo() {
            var redirectUrl = '{{ route('usersmanage.addmanyusersview') }}';
            window.location = redirectUrl;
        }
    </script>
@endsection
