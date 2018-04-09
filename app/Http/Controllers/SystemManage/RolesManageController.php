<?php

namespace App\Http\Controllers\SystemManage;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RolesManageController extends Controller
{
    //角色管理模块

    /**
     * 角色管理首页展示
     */
    public function index() {
        $pageData = [];
        //查找全部的角色信息
        $pageData['roles'] = Role::join('users', 'roles.updater_id', '=', 'users.id')
                                   ->select('roles.id', 'roles.name', 'roles.description', 'roles.created_at', 'roles.updated_at', 'roles.is_valid', 'users.name as updater')
                                   ->get();
        return view('systemManage.rolesManage.index') ->with($pageData);
    }

    /**
     * 添加角色
     */
    public function addRole(Request $request) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'role_name.required' =>'角色名不能为空',
            'role_name.unique' =>'该角色已经存在',
            'role_name.max' =>'角色名超过最大限度',
            'role_description.max' =>'角色简介超多最大限度'
        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'role_name' =>'bail|required|unique:roles,name|max:128',
            'role_description' =>'bail|nullable|max:256'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }
        //存入表单
        $role = new Role;
        $role ->name = $request ->role_name;
        $role ->description = $request ->role_description;
        $role ->creator_id = Auth::id();
        $role ->updater_id = Auth::id();
        if(!$role ->save()) {
            $retData['error'] = true;
            $retData['msg'] = ['存入数据库失败，请重试'];
            return json_encode($retData);
        }
        else {
            return json_encode($retData);
        }
    }

    /**
     * 弃用角色
     * @param $id 角色id
     * @return ajax信息
     */
    public function disableRole(Request $request, $id) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        if(!$id) {
            $retData['error'] = true;
            $retData['msg'] = ['缺少角色信息，请刷新网页后重试'];
            return json_encode($retData);
        }

        //更新is_valid字段
        $role = Role::find($id);

        //查询是该角色是否有用户
        if($role ->users() ->where('users.is_valid', 1) ->exists()){
            $retData['error'] = true;
            $retData['msg'] = ['该角色存在用户群体，禁止弃用！'];
            return json_encode($retData);
        }
        else {
            $role ->is_valid = 0;
            $role ->updater_id = Auth::id();
            if(!$role ->save()) {
                $retData['error'] = true;
                $retData['msg'] = ['数据库操作失败'];
                return json_encode($retData);
            }
            else {
                return json_encode($retData);
            }
        }

    }

    /**
     * 启用角色
     * @param $id 角色id
     * @return ajax提示信息
     */
    public function startRole(Request $request, $id) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        if(!$id) {
            $retData['error'] = true;
            $retData['msg'] = ['缺少角色信息，请刷新网页后重试'];
            return json_encode($retData);
        }
        //更新is_valid字段
        $role = Role::find($id);
        $role ->is_valid = 1;
        $role ->updater_id = Auth::id();
        if(!$role ->save()) {
            $retData['error'] = true;
            $retData['msg'] = ['数据库操作失败'];
            return json_encode($retData);
        }
        else {
            return json_encode($retData);
        }

    }

    /**
     * 权限展示
     * @param $id 角色id
     * @return 视图
     */
    public function rolePermissions(Request $request, $id) {
        $pageData = [
            'error' =>false,
            'msg' =>'',
            'roleId' =>$id,
        ];
        $role = Role::find($id);

        if($role) {
            //获取角色拥有的权限
            $rolePermissions = $role ->permissions()
                                 ->select('permissions.name as permission_name', 'permissions.description as permission_description')
                                 ->where('permissions.is_valid', '=', '1')
                                 ->get();
            //获取角色拥有的权限的id
            $arrRolePermissionIds = [];
            foreach ($rolePermissions as $permission) {
                array_push($arrRolePermissionIds, $permission ->pivot ->permission_id);
            }

            //获取角色没有的权限
            $notPermissions = Permission::select('id', 'name', 'description', 'updated_at')
                                 ->whereNotIn('id', $arrRolePermissionIds)
                                 ->where('is_valid', '=', '1')
                                 ->get();

            $pageData['rolePermissions'] = $rolePermissions;
            $pageData['notPermissions'] = $notPermissions;
        }
        else {
            $pageData['error'] = true;
            $pageData['msg'] = '该角色信息出错';
        }

        return view('systemManage.rolesManage.rolePermissions') ->with($pageData);
    }


    /**
     * 为角色增加权限
     */
    public function givePermission(Request $request, $roleId) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];

        //检查授权数组是否为空
        if(!empty($request ->arr_permission_gived)) {
            $role = Role::find($roleId);

            //检查角色是否存在
            if($role) {
                $arrSaveData = [];
                foreach ($request ->arr_permission_gived as $permissionId) {
                    $arrSaveData[$permissionId] = ['creator_id' =>Auth::id(), 'updater_id' =>Auth::id()];
                }
                $role ->permissions() ->attach($arrSaveData);
                return json_encode($retData);
            }
            else {
                $retData['error'] = true;
                $retData['msg'] = ['该角色不存在!'];
                return json_encode($retData);
            }
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['请选择需要授于的权限!'];
            return json_encode($retData);
        }
    }

    /**
     * 移除角色的权限
     * @param $roleId 角色id
     * @param $permissionId 权限id
     * @return ajax信息
     */
    public function removePermission($roleId, $permissionId) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $role = Role::find($roleId);
        //判断角色是否存在
        if($role) {
            //判断角色是否有该权限
            if($role ->permissions() ->where('permissions.id', '=', $permissionId) ->exists()) {
                if($role ->permissions() ->detach($permissionId)) {
                    return json_encode($retData);
                }
                else {
                    $retData['error'] = true;
                    $retData['msg'] = ['数据库操作失败！'];
                    return json_encode($retData);
                }
            }
            else {
                $retData['error'] = true;
                $retData['msg'] = ['该角色没有该权限！'];
                return json_encode($retData);
            }
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['该角色不存在！'];
            return json_encode($retData);
        }

    }
}
