<?php

namespace App\Http\Controllers\EducationManage;

use App\Models\Academy;
use App\Models\Major;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class MajorsManageController extends Controller
{
    //专业管理模块

    /**
     * 专业管理首页
     */
    public function index() {
        $pageData = [];
        //查找全部的专业信息
        $pageData['majors'] = Major::with(['updater:id,name', 'academy:id,name'])
            ->where('majors.is_valid', '1')
            ->get();
        //查找全部的学院信息
        $pageData['academies'] = Academy::select('id', 'name')
            ->where('is_valid', '1')
            ->get();

        return view('educationManage.majorsmanage.index') ->with($pageData);
    }

    /**
     * 添加专业
     */
    public function addMajor(Request $request) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'major_unique_id.required' =>'专业编码不能为空',
            'major_unique_id.unique' =>'专业编码已经存在',
            'major_unique_id.integer' =>'专业编码必须为数字',
            'major_name.required' =>'专业名不能为空',
            'major_name.max' =>'专业名超过最大限度',
            'major_description.max' =>'专业简介超多最大限度',
            'major_academy_id.required' =>'所属学院必选',
            'major_academy_id.exists' =>'所属学院不存在'
        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'major_academy_id' =>[
                'bail','required',
                Rule::exists('academies', 'id') ->where(function ($query) use($request){
                    $query ->where('is_valid', '=', 1);
                })
            ],
            'major_unique_id' =>'bail|required|integer|unique:majors,unique_id',
            'major_name' =>'bail|required|max:100',
            'major_description' =>'bail|nullable|max:128',
        ], $message);

        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }
        //存入表单
        $major = new Major;
        $major ->unique_id = intval($request ->major_unique_id);
        $major ->name = $request ->major_name;
        $major ->description = $request ->major_description;
        $major ->academy_id = $request ->major_academy_id;
        $major ->creator_id = Auth::id();
        $major ->updater_id = Auth::id();
        if(!$major ->save()) {
            $retData['error'] = true;
            $retData['msg'] = ['存入数据库失败，请重试'];
            return json_encode($retData);
        }
        else {
            return json_encode($retData);
        }
    }

    /**
     * 编辑专业页面
     */
    public function editMajorView($majorId) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];

        //获取专业信息
        $major = Major::find($majorId) ;
        if(is_null($major)) {
            $pageData['error'] = true;
            $pageData['msg'] = '该专业不存在！';
        }
        else {
            $pageData['major'] = $major;
        }
        //获取学院信息
        $pageData['academies'] = Academy::select('id', 'name') ->where('is_valid', '=', 1) ->get();
        return view('educationManage.majorsManage.editMajor') ->with($pageData);

    }

    /**
     * 更新专业信息
     */
    public function updateMajorInfo(Request $request, $majorId) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'major_unique_id.required' =>'专业编码不能为空',
            'major_unique_id.integer' =>'专业编码必须为数字',
            'major_name.required' =>'专业名不能为空',
            'major_name.max' =>'专业名超过最大限度',
            'major_description.max' =>'专业简介超多最大限度',
            'major_academy_id.required' =>'所属学院必选',
            'major_academy_id.exists' =>'所属学院不存在',
            'major_valid.required' =>'专业状态必选',
            'major_valid.boolean' =>'专业状态只有两种值'
        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'major_unique_id' =>'bail|required|integer',
            'major_name' =>'bail|required|max:100',
            'major_description' =>'bail|nullable|max:128',
            'major_valid' =>'bail|required|boolean',
            'major_academy_id' =>[
                'bail','required',
                Rule::exists('academies', 'id') ->where(function ($query) use($request){
                    $query ->where('is_valid', '=', 1);
                })
            ],
        ], $message);

        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }
        $major = Major::find($majorId);
        //判断更新后的unique_id是否唯一
        if($major ->where([['id', '<>', $majorId], ['unique_id', '=', $request ->major_unique_id], ['is_valid', '=', '1']]) ->exists()) {
            $retData['error'] = true;
            $retData['msg'] = ['变更后的编码已存在！'];
            return json_encode($retData);
        }
        //如果要弃用，判断当前专业是否有所属班级
        if($request ->major_valid == false && count($major ->schoolClasses) > 0) {
            $retData['error'] = true;
            $retData['msg'] = ['当前专业拥有相关班级，禁止弃用！'];
            return json_encode($retData);
        }
        //更新表单
        $saveRet = $major ->update([
            'unique_id' =>intval($request ->major_unique_id),
            'name' =>$request ->major_name,
            'description' =>$request ->major_description,
            'is_valid' => $request ->major_valid,
            'academy_id' =>$request ->major_academy_id,
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
}
