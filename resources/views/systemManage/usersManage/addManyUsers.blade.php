@extends('layouts.baseFrame')
@section('content')
    <div id="add-user-div" class="panel panel-primary" style="width: 100%;">
        <div class="panel-heading">
            <h3>添加单个用户</h3>
        </div>
        <div class="panel-body">
            <form id="add-users-form" method="post" action="{{ route('usersmanage.confirmusersinfo') }}" class="form-group" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="user-belong-type">集体类型</label>
                    <select name="user_belong_type" id="user-belong-type" class="form-control" onchange="selectOnChange('user-belong-type', 'user-belong-id', '{{ $userBelongs }}')">
                        @foreach($belongTypes as $key =>$type)
                            <option value="{{ $key }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="user-belong-id">集体名称</label>
                    <select name="user_belong_id" id="user-belong-id" class="form-control">
                    </select>
                </div>
                <div class="form-group">
                    <label for="user-file">用户名单Excel</label>
                    <input type="file" id="user-file" name="user_file">
                </div>
                <button type="submit" class="btn btn-primary">&nbsp;添&nbsp;加&nbsp;</button>
            </form>
        </div>
    </div>
@endsection

@section('js-text-part')
    <script type="text/javascript">
    </script>
@endsection
