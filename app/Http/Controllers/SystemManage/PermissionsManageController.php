<?php

namespace App\Http\Controllers\SystemManage;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PermissionsManageController extends Controller
{
    //权限管理模块

    /**
     * 权限管理首页
     */
    public function index(Request $request) {
        $pageData = [];
        //查找全部的角色信息
        $pageData['permissions'] = Permission::join('users', 'permissions.updater_id', '=', 'users.id')
            ->select('permissions.id', 'permissions.name', 'permissions.description', 'permissions.created_at', 'permissions.updated_at', 'permissions.is_valid', 'users.name as updater')
            ->get();

        return view('systemManage.permissionsManage.index') ->with($pageData);
    }

    /**
     * 增加权限
     */
    public function addPermission(Request $request) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'permission_name.required' =>'权限名不能为空',
            'permission_name.unique' =>'该权限名已经存在',
            'permission_name.max' =>'权限名超过最大限度',
            'permission_description.max' =>'权限简介超多最大限度',
            'permission_controller.required' =>'controller不能为空',
            'permission_controller.alpha' =>'controller必须由字母组成',
            'permission_controller.max' =>'controller超过最大限度',
            'permission_function.required' =>'function不能为空',
            'permission_function.alpha' =>'function必须由字母组成',
            'permission_function.max' =>'function超过最大限度'
        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'permission_name' =>'bail|required|unique:permissions,name|max:128',
            'permission_description' =>'bail|nullable|max:256',
            'permission_controller' =>'bail|required|alpha|max:64',
            'permission_function' =>'bail|required|alpha|max:64'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }

        $permission = new Permission;
        //验证controller@action是否已经存在数据库中
        if($permission ->where([ ['controller', '=', $request ->permission_controller], ['function', '=', $request ->permission_function]]) ->exists()) {
            $retData['error'] = true;
            $retData['msg'] = ['该controller@function记录已经存在'];
            return json_encode($retData);
        }
        else {
            //存入表单
            $permission ->name = $request ->permission_name;
            $permission ->description = $request ->permission_description;
            $permission ->controller = strtolower($request ->permission_controller);
            $permission ->function = strtolower($request ->permission_function);
            $permission ->creator_id = Auth::id();
            $permission ->updater_id = Auth::id();
            if(!$permission ->save()) {
                $retData['error'] = true;
                $retData['msg'] = ['数据库操作失败，请重试'];
                return json_encode($retData);
            }
            else {
                return json_encode($retData);
            }

        }
    }

    /**
     * 编辑权限视图
     */
    public function editPermissionView($permissionId) {
        $pageData = [
            'error' =>false,
            'msg' =>''
        ];

        $permission = Permission::find($permissionId);
        if(is_null($permission)) {
            $pageData['error'] = true;
            $pageData['msg'] = '该角色不存在！';
        }
        else {
            $pageData['permission'] = $permission;
        }
        return view('systemManage.permissionsmanage.editpermission') ->with($pageData);
    }

    /**
     * 更新权限信息
     */
    public function updatePermissionInfo(Request $request, $permissionId) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'permission_name.required' =>'权限名不能为空',
            'permission_name.max' =>'权限名超过最大限度',
            'permission_description.max' =>'权限简介超多最大限度',
            'permission_controller.required' =>'controller不能为空',
            'permission_controller.alpha' =>'controller必须由字母组成',
            'permission_controller.max' =>'controller超过最大限度',
            'permission_function.required' =>'function不能为空',
            'permission_function.alpha' =>'function必须由字母组成',
            'permission_function.max' =>'function超过最大限度',
            'permission_valid.required' =>'状态信息必选',
            'permission_valid.boolean' =>'状态信息之支持两种状态'

        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'permission_name' =>'bail|required|max:128',
            'permission_description' =>'bail|nullable|max:256',
            'permission_controller' =>'bail|required|alpha|max:64',
            'permission_function' =>'bail|required|alpha|max:64',
            'permission_valid' =>'bail|required|boolean'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }
        $permission = Permission::find($permissionId);
        if($permission) {
            $saveRet = $permission ->update([
                'name' =>$request ->permission_name,
                'description' =>$request ->permission_description,
                'controller' =>$request ->permission_controller,
                'function' =>$request ->permission_function,
                'is_valid' =>boolval($request ->permission_valid),
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
            $retData['msg'] = ['该权限不存在！'];
            return json_encode($retData);
        }
    }

}
