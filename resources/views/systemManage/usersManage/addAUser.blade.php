@extends('layouts.baseFrame')
@section('content')
    <div id="add-user-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加单个用户</h3>
        </div>
        <div class="panel-body">
            <form id="add-user-form" method="post" action="{{ route('usersmanage.addauser') }}" class="form-group" onsubmit="return false;">
                @csrf
                <div class="form-group">
                    <label for="user-unique-id">用户编号</label>
                    <input type="text" class="form-control" id="user-unique-id" name="user_unique_id" placeholder="请输入用户编号">
                </div>
                <div class="form-group">
                    <label for="user-name">用户名称</label>
                    <input type="text" class="form-control" id="user-name" name="user_name" placeholder="请输入用户姓名">
                </div>
                <div class="form-group">
                    <label for="user-email">用户邮箱</label>
                    <input type="email" class="form-control" id="user-email" name="user_email" placeholder="请输入用户邮箱">
                </div>
                <div class="form-group">
                    <label for="user-mobile">用户手机号</label>
                    <input type="tel" class="form-control" id="user-mobile" name="user_mobile" placeholder="请输入用户手机号">
                </div>
                <div class="form-group">
                    <label for="user-belong-type">集体类型</label>
                    <select name="user_belong_type" id="user-belong-type" class="form-control" onchange="selectOnChange('user-belong-type', 'user-belong-id', '{{ $userBelongs }}')">
                        @foreach($belongTypes as $key =>$type)
                            <option value="{{ $key }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="user-belong-type">集体名称</label>
                    <select name="user_belong_id" id="user-belong-id" class="form-control">
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('add-user-form')">&nbsp;添&nbsp;加&nbsp;</button>
            </form>
        </div>
    </div>
@endsection

@section('js-text-part')
    <script type="text/javascript">
    </script>
@endsection
