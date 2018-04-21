<?php

namespace App\Http\Controllers\EducationManage;

use App\Models\Academy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AcademiesManageController extends Controller
{
    //学院管理模块

    /**
     * 学院管理首页
     */
    public function index() {
        $pageData = [];
        //查找全部的学院信息
        $pageData['academies'] = Academy::join('users', 'academies.updater_id', '=', 'users.id')
            ->select('academies.id', 'academies.unique_id', 'academies.name', 'academies.description', 'academies.created_at', 'academies.updated_at', 'academies.is_valid', 'users.name as updater')
            ->get();

        return view('educationManage.academiesManage.index') ->with($pageData);
    }

    /**
     * 添加学院
     */
    public function addAcademy(Request $request) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'academy_unique_id.required' =>'学院编码不能为空',
            'academy_unique_id.unique' =>'学院编码已经存在',
            'academy_unique_id.integer' =>'学院编码必须为数字',
            'academy_name.required' =>'学院名不能为空',
            'academy_name.max' =>'学院名超过最大限度',
            'academy_description.max' =>'学院简介超多最大限度',
        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'academy_unique_id' =>'bail|required|unique:academies,unique_id|integer',
            'academy_name' =>'bail|required|max:100',
            'academy_description' =>'bail|nullable|max:128',
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }
        //存入表单
        $academy = new Academy;
        $academy ->unique_id = intval($request ->academy_unique_id);
        $academy ->name = $request ->academy_name;
        $academy ->description = $request ->academy_description;
        $academy ->creator_id = Auth::id();
        $academy ->updater_id = Auth::id();
        if(!$academy ->save()) {
            $retData['error'] = true;
            $retData['msg'] = ['存入数据库失败，请重试'];
            return json_encode($retData);
        }
        else {
            return json_encode($retData);
        }
    }

    /**
     * 编辑学院信息视图
     */
    public function editAcademyView($academyId) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];

        $academy = Academy::find($academyId);
        if(is_null($academy)) {
            $pageData['error'] = true;
            $pageData['msg'] = '该学院不存在！';
        }
        else {
            $pageData['academy'] = $academy;
        }
        return view('educationManage.academiesManage.editAcademy') ->with($pageData);
    }

    /**
     * 更新学院信息
     */
    public function updateAcademyInfo(Request $request, $academyId) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'academy_unique_id.required' =>'学院编码不能为空',
            'academy_unique_id.integer' =>'学院编码必须为数字',
            'academy_name.required' =>'学院名不能为空',
            'academy_name.max' =>'学院名超过最大限度',
            'academy_description.max' =>'学院简介超多最大限度',
            'academy_valid.required' =>'状态信息必选',
            'academy_valid.boolean' =>'状态信息之支持两种状态'

        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'academy_unique_id' =>'bail|required|integer',
            'academy_name' =>'bail|required|max:100',
            'academy_description' =>'bail|nullable|max:128',
            'academy_valid' =>'bail|required|boolean'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }
        $academy = Academy::find($academyId);
        if($academy) {
            //检测是否便跟后编号重复
            if($academy ->where([['id', '<>', $academyId], ['is_valid', '=', 1], ['unique_id', '=', $request ->academy_unique_id]]) ->exists()) {
                $retData['error'] = true;
                $retData['msg'] = ['更改后的学院编号已存在！'];
                return json_encode($retData);
            }
            //查看该学院是否有相关的专业，如果有相关专业，则禁止弃用学院
            if($request ->academy_valid == false) {
                if($academy ->majors() ->where('majors.is_valid', '1') ->exists()) {
                    $retData['error'] = true;
                    $retData['msg'] = ['该学院尚包含一些专业，禁止弃用！'];
                    return json_encode($retData);
                }

            }
            $saveRet = $academy ->update([
                'unique_id' =>$request ->academy_unique_id,
                'name' =>$request ->academy_name,
                'description' =>$request ->academy_description,
                'is_valid' =>boolval($request ->academy_valid),
                'updater_id' =>Auth::id()
            ]);

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
            $retData['msg'] = ['该学院不存在！'];
            return json_encode($retData);
        }
    }
}
