<?php

namespace App\Http\Controllers\EducationManage;

use App\Models\Major;
use App\Models\SchoolClass;
use App\User;
use Illuminate\Foundation\Providers\FormRequestServiceProvider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ClassesManageController extends Controller
{
    //班级管理模块

    /**
     * 批量导入班级信息时暂存于session的键名
     */
    private $excelSessionKey = 'classesExcel';

    /**
     * 班级管理首页
     */
    public function index() {
        $pageData = [];
        //查找全部的班级信息
        $pageData['classes'] = SchoolClass::with(['updater:id,name', 'major:id,name'])
            ->where('classes.is_valid', '1')
            ->get();
        //查找全部的专业信息
        $pageData['majors'] = Major::select('id', 'name')
            ->where('is_valid', '1')
            ->get();
        //年级信息
        $pageData['grades'] = $this ->getGrades();

        return view('educationManage.classesmanage.index') ->with($pageData);
    }

    /**
     * 添加单个班级
     */
    public function addClass(Request $request) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'class_unique_id.required' =>'班级编码不能为空',
            'class_unique_id.unique' =>'班级编码已经存在',
            'class_unique_id.integer' =>'班级编码必须为数字',
            'class_name.required' =>'班级名不能为空',
            'class_name.max' =>'班级名超过最大限度',
            'class_description.max' =>'班级简介超多最大限度',
            'class_major_id.required' =>'所属专业必选',
            'class_major_id.exists' =>'所属专业不存在',
            'class_grade.required' =>'年级信息必选',
            'class_grade.integer' =>'年级信息格式错误'
        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'class_grade' =>'bail|integer',
            'class_major_id' =>[
                'bail','required',
                Rule::exists('majors', 'id') ->where(function ($query) use($request){
                    $query ->where('is_valid', '=', 1);
                }),
            ],
            'class_unique_id' =>'bail|required|integer|unique:classes,unique_id',
            'class_name' =>'bail|required|max:100',
            'class_description' =>'bail|nullable|max:128',
        ], $message);

        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }
        //存入表单
        $schoolClass = new SchoolClass;
        $schoolClass ->unique_id = intval($request ->class_unique_id);
        $schoolClass ->name = $request ->class_name;
        $schoolClass ->description = $request ->class_description;
        $schoolClass ->major_id = $request ->class_major_id;
        $schoolClass ->grade = intval($request ->class_grade);
        $schoolClass ->creator_id = Auth::id();
        $schoolClass ->updater_id = Auth::id();
        if(!$schoolClass ->save()) {
            $retData['error'] = true;
            $retData['msg'] = ['存入数据库失败，请重试'];
            return json_encode($retData);
        }
        else {
            return json_encode($retData);
        }
    }

    /**
     * 编辑班级信息视图
     */
    public function editClassView(Request $request, $classId) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];

        //获取班级信息
        $schoolClass = SchoolClass::find($classId) ;
        if(is_null($schoolClass)) {
            $pageData['error'] = true;
            $pageData['msg'] = '该班级不存在！';
        }
        else {
            $pageData['class'] = $schoolClass;
        }
        //获取专业信息
        $pageData['majors'] = Major::select('id', 'name') ->where('is_valid', '=', 1) ->get();
        //获取年级信息
        $pageData['grades'] = $this ->getGrades();
        return view('educationManage.classesManage.editClass') ->with($pageData);
    }

    /**
     * 更新班级信息
     */
    public function updateClassInfo(Request $request, $classId) {

        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'class_unique_id.required' =>'班级编码不能为空',
            'class_unique_id.unique' =>'更改后的班级编码已经存在',
            'class_unique_id.integer' =>'班级编码必须为数字',
            'class_name.required' =>'班级名不能为空',
            'class_name.max' =>'班级名超过最大限度',
            'class_description.max' =>'班级简介超多最大限度',
            'class_major_id.required' =>'所属专业必选',
            'class_major_id.exists' =>'所属专业不存在',
            'class_grade.required' =>'年级信息必选',
            'class_grade.integer' =>'年级信息格式错误'
        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'class_unique_id' =>[
                'bail', 'required', 'integer',
                Rule::unique('classes', 'unique_id') ->where(function ($query) use($classId){
                    $query ->where('id', '<>', $classId);
                })
            ],
            'class_name' =>'bail|required|max:100',
            'class_description' =>'bail|nullable|max:128',
            'class_valid' =>'bail|required|boolean',
            'class_grade' =>'bail|integer',
            'class_major_id' =>[
                'bail','required',
                Rule::exists('majors', 'id') ->where(function ($query){
                    $query ->where('is_valid', '=', 1);
                })
            ],
        ], $message);

        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }
        $schoolClass = SchoolClass::find($classId);
        //如果要弃用，判断当前班级是否包含学生
        if($request ->class_valid == false &&
            (User::where([['belong_type', '=', User::$belong_type_class], ['belong_id', '=', $classId], ['is_valid', '=', 1]])
                ->exists())) {
            $retData['error'] = true;
            $retData['msg'] = ['当前班级拥有用户成员，禁止弃用！'];
            return json_encode($retData);
        }
        //更新表单
        $saveRet = $schoolClass ->update([
            'unique_id' =>intval($request ->class_unique_id),
            'name' =>$request ->class_name,
            'description' =>$request ->class_description,
            'is_valid' => $request ->class_valid,
            'grade' =>$request ->class_grade,
            'major_id' =>$request ->class_major_id,
            'updater_id' =>Auth::id()
        ]);
        if(!$saveRet) {
            $retData['error'] = true;
            $retData['msg'] = ['存入数据库失败，请重试'];
            return json_encode($retData);
        }
        else {
            return json_encode($retData);
        }
    }

    /**
     * 下载批量导入班级信息excel文件实例
     */
    public function downloadExcelExample() {
        return Storage::disk('public') ->download('examples/classes_upload_example.xlsx', 'classes_upload_example.xlsx');
    }

    /**
     * 批量添加班级信息--解析文件中班级信息后展示
     */
    public function uploadClasses(Request $request) {
        $pageData = [
            'error' =>false,
            'msg' =>[],
        ];
        $message = [
            'class_major_id.required' =>'所属专业必选',
            'class_major_id.exists' =>'所属专业不存在',
        ];
        $validator = Validator::make($request ->all(), [
            'class_major_id' =>[
                'bail','required',
                Rule::exists('majors', 'id') ->where(function ($query) use($request){
                    $query ->where('is_valid', '=', 1);
                }),
            ],
        ], $message);
        if($validator ->fails()) {
            $pageData['error'] = true;
            $pageData['msg'] = $validator ->errors() ->all();
            return view('educationManage.classesManage.confirmClasses') ->with($pageData);
        }
        if(!$request ->hasFile('class_file') || !$request ->class_file ->isValid()) {
            $pageData['error'] = true;
            $pageData['msg'] = ['上传文件失败！'];
            return view('educationManage.classesManage.confirmClasses') ->with($pageData);
        }
        $fileExtension = $request ->class_file ->getClientOriginalExtension();
        if(!in_array($fileExtension, config('excel.support_extension_list'))) {
            $pageData['error'] = true;
            $pageData['msg'] = ['该文件上传仅支持xls、xlsx两种格式的文件'];
            return view('educationManage.classesManage.confirmClasses') ->with($pageData);
        }
        //存储文件
        $path = $request ->class_file ->store('temporary', 'public');
        $fullPath = storage_path('app/public') . '/' . $path;

        $excelData = [];

        //获取表格数据
        Excel::load($fullPath, function ($reader) use(&$excelData){
            $reader = $reader ->getSheet(0);
            $excelData = $reader ->toArray();
        });
        //表格数据判空
        if(empty($excelData)) {
            $pageData['error'] = true;
            $pageData['msg'] = ['表格数据为空'];
            Storage::disk('public') ->delete($path);
            return view('educationManage.classesManage.confirmClasses') ->with($pageData);
        }
        //获取专业信息
        $majorInfo = Major::select('id', 'name')
            ->where([['is_valid', '=', 1], ['id', '=', $request ->class_major_id]])
            ->first();
        //组织班级信息
        $classes = [];
        for($i = 1; $i < count($excelData); $i++) {
            $classes[$i]['class_unique_id'] = intval($excelData[$i][0]);
            $classes[$i]['class_name'] = $excelData[$i][1];
            $classes[$i]['class_description'] = $excelData[$i][2];
            $classes[$i]['class_major_name'] = $majorInfo ->name;
            $classes[$i]['class_major_id'] = intval($majorInfo ->id);
            $classes[$i]['class_grade'] = intval($excelData[$i][3]);
        }
        $pageData['classes'] = $classes;
        //将文件中的数据保存在session中
        $request ->session() ->put($this ->excelSessionKey, serialize($classes));
        Storage::disk('public') ->delete($path);
        return view('educationManage.classesManage.confirmClasses') ->with($pageData);
    }

    /**
     * 批量将session中的班级信息存入数据库
     */
    public function addClassesFromSession(Request $request) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];

        if($request ->session() ->has($this ->excelSessionKey)) {
            $classesInfo = unserialize($request ->session() ->get($this ->excelSessionKey));
            //删除session数据
            $request ->session() ->forget($this ->excelSessionKey);
            //检查unique_id
            $uniqueIds = array_column($classesInfo, 'class_unique_id');
            if(!empty($uniqueIds) && is_array($uniqueIds)) {
                if(count(array_unique($uniqueIds)) < count($uniqueIds)) {
                    $retData['error'] = true;
                    $retData['msg'] = ['excel中部分班级编码重复！'];
                    return json_encode($retData);
                }
                if(SchoolClass::whereIn('unique_id', $uniqueIds) ->exists()) {
                    $retData['error'] = true;
                    $retData['msg'] = ['excel中部分班级编码在系统中已经存在！'];
                    return json_encode($retData);
                }
                $saveItems = [];
                foreach ($classesInfo as $key =>$item) {
                    $saveItems[$key]['unique_id'] = $item['class_unique_id'];
                    $saveItems[$key]['name'] = $item['class_name'];
                    $saveItems[$key]['description'] = $item['class_description'];
                    $saveItems[$key]['grade'] = $item['class_grade'];
                    $saveItems[$key]['major_id'] = $item['class_major_id'];
                    $saveItems[$key]['creator_id'] = Auth::id();
                    $saveItems[$key]['updater_id'] = Auth::id();
                    $saveItems[$key]['created_at'] = date('Y-m-d H:i:s', time());
                    $saveItems[$key]['updated_at'] = date('Y-m-d H:i:s', time());
                }

                $saveRet = SchoolClass::insert($saveItems);
                if($saveRet) {
                    return json_encode($retData);
                }
                else {
                    $retData['error'] = true;
                    $retData['msg'] = ['数据库操作失败，请重试！'];
                    return json_encode($retData);
                }
            }
            else {
                $retData['error'] = true;
                $retData['msg'] = ['数据出现问题，请重试'];
                return json_encode($retData);
            }
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['缓存数据丢失，请重新上传'];
            return json_encode($retData);
        }
    }
}
