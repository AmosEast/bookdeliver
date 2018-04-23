<?php

namespace App\Http\Controllers\EducationManage;

use App\Models\Course;
use App\Models\Major;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class CoursesManageController extends Controller
{
    //课程管理

    /**
     * 课程管理首页
     */
    public function index() {
        $pageData = [];
        //获取所有的课程
        $pageData['courses'] = Course::all();
        //获取所有的专业
        $pageData['majors'] = Major::select('id', 'name') ->where('is_valid', '=', 1) ->get();

        return view('educationManage.coursesManage.index') ->with($pageData);
    }

    /**
     * 添加课程
     */
    public function addCourse(Request $request) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];

        $message = [
            'course_name.required' =>'课程名称不能为空',
            'course_name.unique' =>'该课程已经存在',
            'course_name.max' =>'课程名称超过最大限度',
            'course_description.max' =>'课程描述超过最大限度',
            'course_majors.required' =>'课程所属专业必选',
            'course_majors.array' =>'课程所属专业格式错误'
        ];
        $validator = Validator::make($request ->all(), [
            'course_name' =>'bail|required|unique:courses,name|max:128',
            'course_description' =>'bail|nullable|max:128',
            'course_majors' =>'bail|required|array'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return response() ->json($retData);
        }
        //存储course
        DB::beginTransaction();
        $course = new Course;
        $course ->name = $request ->course_name;
        $course ->description = $request ->course_description;
        $course ->creator_id = Auth::id();
        $course ->updater_id = Auth::id();
        try{
            $course ->save();
            foreach ($request ->course_majors as $major) {
                $course ->majors() ->attach($major, ['creator_id' =>Auth::id(), 'updater_id' =>Auth::id()]);
            }
            DB::commit();
            return response() ->json($retData);
        }catch (Exception $exception) {
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败！'];
            return response() ->json($retData);
        }
    }

    /**
     * 查看课程所属专业
     */
    public function getMajors($courseId) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $majors = Course::find($courseId) ->majors() ->where('majors.is_valid', '=', 1) ->pluck('name');
        if($majors) {
            $retData['data'] = $majors;
            return response() ->json($retData);
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['查询数据错误！'];
            return response() ->json($retData);
        }

    }

    /**
     * 编辑课程信息页面
     */
    public function editCourseView($courseId) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];
        //获取课程信息
        $course = Course::with(['majors' =>function ($query){
            $query ->where('majors.is_valid', '=', 1);
        }]) ->find($courseId);
        //获取课程所属的major的id
        $courseMajorIds = [];
        foreach ($course ->majors as $major) {
            $courseMajorIds[] = $major ->id;
        }
        //获取所有专业信息
        $majors = Major::select('id', 'name') ->where('is_valid', '=', 1) ->get();
        $pageData['course'] = $course;
        $pageData['majors'] = $majors;
        $pageData['courseMajorIds'] = $courseMajorIds;
        return view('educationManage.coursesManage.editCourse') ->with($pageData);
    }

    /**
     * 更新课程信息
     */
    public function updateCourse(Request $request, $courseId) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];

        $message = [
            'course_name.required' =>'课程名称不能为空',
            'course_name.unique' =>'更改后的课程名称已经存在',
            'course_name.max' =>'课程名称超过最大限度',
            'course_description.max' =>'课程描述超过最大限度',
            'course_majors.required' =>'课程所属专业必选',
            'course_majors.array' =>'课程所属专业格式错误',
            'course_valid.required' =>'课程状态必选',
            'course_valid.boolean' =>'课程状态只有两种状态'
        ];
        $validator = Validator::make($request ->all(), [
            'course_name' =>[
                'bail', 'required', 'max:128',
                Rule::unique('courses', 'name') ->where(function ($query) use($courseId){
                    $query ->where([
                        ['is_valid', '=', 1],
                        ['id', '<>', $courseId]
                    ]);
                })
            ],
            'course_description' =>'bail|nullable|max:128',
            'course_majors' =>'bail|required|array',
            'course_valid' =>'bail|required|boolean'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return response() ->json($retData);
        }

        $course = Course::find($courseId);
        if($course) {
            DB::beginTransaction();
            $course ->name = $request ->course_name;
            $course ->description = $request ->course_description;
            $course ->is_valid =$request ->course_valid;
            $course ->updater_id = Auth::id();
            if($course ->save()) {
                if($course ->majors() ->detach()) {
                    $attachData = [];
                    foreach ($request ->course_majors as $majorId) {
                        $attachData[$majorId] = ['creator_id' =>Auth::id(), 'updater_id' =>Auth::id()];
                    }
                    $course ->majors() ->attach($attachData);
                    DB::commit();
                    return response() ->json($retData);
                }
                else {
                    DB::rollBack();
                    $retData['error'] = true;
                    $retData['msg'] = ['数据库操作失败2！'];
                    return response() ->json($retData);
                }
            }
            else {
                DB::rollBack();
                $retData['error'] = true;
                $retData['msg'] = ['数据库操作失败3！'];
                return response() ->json($retData);
            }
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['该课程不存在'];
            return response() ->json($retData);
        }
    }

}
