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
        <div id="edit-course-div" class="panel panel-primary" style="width: 100%;">
            <div class="panel-heading">
                <h3>编辑课程</h3>
            </div>
            <div class="panel-body">
                <form id="edit-course-form" action="{{ route('coursesmanage.updatecourse', ['courseId' =>$course ->id]) }}" method="post" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label for="course-name">课程名称</label>
                        <input type="text" class="form-control" id="course-name" name="course_name" value="{{ $course ->name }}">
                    </div>
                    <div class="form-group">
                        <label for="course-description">课程简介</label>
                        <input type="text" class="form-control" id="course-name" name="course_description" value="{{ $course ->description }}">
                    </div>
                    <div class="form-group">
                        <label for="course-majors">所属专业</label>
                        <select name="course_majors[]" id="course-majors" class="selectpicker show-menu-arrow form-control bs-select-hidden" multiple="multiple" data-live-search="true" title="请选择课程所属专业">
                            @foreach($majors as $major)
                                <option value="{{ $major ->id }}" @if(in_array($major ->id, $courseMajorIds)) selected @endif >{{ $major ->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group has-warning">
                        <label for="course_valid">课程状态</label>
                        <select class="form-control" id="course_valid" name="course_valid">
                            @if($course ->is_valid == 1)
                                <option value="1" selected>启用</option>
                                <option value="0">弃用</option>
                            @else
                                <option value="1">启用</option>
                                <option value="0" selected>弃用</option>
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="ajaxFormSubmit('edit-course-form')">&nbsp;保&nbsp;存&nbsp;</button>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js-text-part')
@endsection
