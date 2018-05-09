<?php

namespace App\Http\Controllers\SystemManage;

use App\Models\Academy;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class UsersManageController extends Controller
{
    //用户管理模块

    //用户上传excel文件在session中保存的键值
    private $excelSessionKey = 'usersExcel';

    /**
     * 用户列表
     *
     * 还差分页和查询功能
     */
    public function index() {
        //获取所有用户信息
        $users = User::select('id', 'unique_id', 'name', 'email', 'mobile', 'is_valid', 'updated_at') ->get();
        $pageData['users'] = $users;
        return view('systemManage.usersManage.index') ->with($pageData);
    }

    /**
     * 用户角色展示
     */
    public function userRoles($userId) {
        $pageData = [
            'error' =>false,
            'msg' =>'',
            'userId' =>$userId
        ];
        //检查用户是否存在
        $user = User::find($userId);
        if($user) {
            //获取用户拥有的角色
            $roles = $user ->roles()
                           ->select('roles.name', 'roles.description')
                           ->where('roles.is_valid', '=', '1')
                           ->get();
            //获取用户拥有的角色的id
            $arrRoleIds = [];
            foreach ($roles as $role) {
                array_push($arrRoleIds, $role ->pivot ->role_id);
            }

            //获取用户没有的角色
            $notRoles = Role::select('id', 'name', 'description', 'updated_at')
                              ->whereNotIn('id', $arrRoleIds)
                              ->where('is_valid', '=', '1')
                              ->get();
            //传给前端页面
            $pageData['userRoles'] = $roles;
            $pageData['notRoles'] = $notRoles;
        }
        else {
            $pageData['error'] = true;
            $pageData['msg'] = '该用户不存在';
        }
        return view('systemManage.usersManage.userRoles') ->with($pageData);
    }

    /**
     * 移除用户角色
     */
    public function removeRole($userId, $roleId) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        $user = User::find($userId);
        //判断用户是否存在
        if($user) {
            //判断用户是否有该角色
            if($user ->roles() ->where('roles.id', '=', $roleId) ->exists()) {
                if($user ->roles() ->detach($roleId)) {
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
                $retData['msg'] = ['该用户没有该角色！'];
                return json_encode($retData);
            }
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['该用户不存在！'];
            return json_encode($retData);
        }
    }

    /**
     * 给用户添加角色
     */
    public function giveRole(Request $request, $userId) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];

        //检查角色数组是否为空
        if(!empty($request ->arr_role_gived)) {
            $user = User::find($userId);

            //检查用户是否存在
            if($user) {
                $arrSaveData = [];
                foreach ($request ->arr_role_gived as $userId) {
                    $arrSaveData[$userId] = ['creator_id' =>Auth::id(), 'updater_id' =>Auth::id()];
                }
                $user ->roles() ->attach($arrSaveData);
                return json_encode($retData);
            }
            else {
                $retData['error'] = true;
                $retData['msg'] = ['该用户不存在!'];
                return json_encode($retData);
            }
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['请选择需要分配的角色!'];
            return json_encode($retData);
        }
    }

    /**
     * 添加单个用户视图
     */
    public function addAUserView(Request $request) {
        //获取集体类型
        $pageData['belongTypes'] = User::getBelongTypeMeaning();
        //获取所有的班级信息
        $pageData['classes'] = SchoolClass::select('id', 'name')
            ->where('is_valid', '=', 1)
            ->get();
        //获取所有的学院信息
        $pageData['academies'] = Academy::select('id', 'name')
            ->where('is_valid', '=', 1)
            ->get();
        //获取所有学校信息（只有一条）
        $pageData['schools'] = School::select('id', 'name')
            ->where('is_valid', '=', 1)
            ->get();
        $userBelongs = [
            User::$belong_type_class =>$pageData['classes'],
            User::$belong_type_academy =>$pageData['academies'],
            User::$belong_type_school =>$pageData['schools']
        ];
        $pageData['userBelongs'] = json_encode($userBelongs);

        return view('systemManage.usersManage.addAUser') ->with($pageData);
    }

    /**
     * 添加单个用户
     */
    public function addAUser(Request $request) {
        //返回数据格式
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        //错误消息定义
        $message = [
            'user_unique_id.required' =>'用户编码为空',
            'user_unique_id.unique' =>'用户编码已经存在',
            'user_unique_id.integer' =>'用户编码必须为数字',
            'user_name.required' =>'用户名不能为空',
            'user_name.max' =>'用户超过最大限度',
            'user_email.required' =>'用户邮箱不能为空',
            'user_email.email' =>'用户邮箱格式错误',
            'user_mobile.required' =>'用户手机号不能为空',
            'user_mobile.numeric' =>'用户手机号格式错误',
            'user_belong_type.required' =>'用户集体类型必选',
            'user_belong_type.in' =>'用户集体类型错误',
            'user_belong_id.required' =>'用户集体必选',
        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'user_unique_id' =>'bail|required|unique:users,unique_id|integer',
            'user_name' =>'bail|required|max:128',
            'user_email' =>'bail|required|email',
            'user_mobile' =>'bail|required|numeric',
            'user_belong_type' =>[
                'bail', 'required',
                Rule::in(array_keys(User::getBelongTypeMeaning()))
            ],
            'user_belong_id' =>'bail|required'
        ], $message);
        if($validator ->fails()) {
            $retData['error'] = true;
            $retData['msg'] = $validator ->errors() ->all();
            return json_encode($retData);
        }
        if(strlen($request ->user_mobile) != 11) {
            $retData['error'] = true;
            $retData['msg'] = ['用户手机号格式错误'];
            return json_encode($retData);
        }
        //存入表单
        $user = new User;
        $user ->unique_id = $request ->user_unique_id;
        $user ->name = $request ->user_name;
        $user ->email = $request ->user_email;
        $user ->mobile = $request ->user_mobile;
        $user ->belong_type = $request ->user_belong_type;
        $user ->belong_id = $request ->user_belong_id;
        $user ->creator_id = Auth::id();
        $user ->updater_id = Auth::id();
        if(!$user ->save()) {
            $retData['error'] = true;
            $retData['msg'] = ['存入数据库失败，请重试'];
            return json_encode($retData);
        }
        else {
            return json_encode($retData);
        }
    }

    /**
     * 下载用户excel示例
     */
    public function downloadExcelExample() {
        return Storage::disk('public') ->download('examples/users_upload_example.xlsx', 'users_upload_example.xlsx');
    }

    /**
     * 批量添加用户视图
     */
    public function addManyUsersView() {
        //获取集体类型
        $pageData['belongTypes'] = User::getBelongTypeMeaning();
        //获取所有的班级信息
        $pageData['classes'] = SchoolClass::select('id', 'name')
            ->where('is_valid', '=', 1)
            ->get();
        //获取所有的学院信息
        $pageData['academies'] = Academy::select('id', 'name')
            ->where('is_valid', '=', 1)
            ->get();
        //获取所有学校信息
        $pageData['schools'] = School::select('id', 'name')
            ->where('is_valid', '=', 1)
            ->get();
        $userBelongs = [
            User::$belong_type_class =>$pageData['classes'],
            User::$belong_type_academy =>$pageData['academies'],
            User::$belong_type_school =>$pageData['schools']
        ];
        $pageData['userBelongs'] = json_encode($userBelongs);

        return view('systemManage.usersManage.addManyUsers') ->with($pageData);
    }

    /**
     * 解析excel文件中内容并展示
     * @param Request $request
     * @return $this
     */
    public function confirmUsersInfo(Request $request) {
        $pageData = [
            'error' =>false,
            'msg' =>[]
        ];
        //表单错误信息
        $message = [
            'user_belong_type.required' =>'用户集体类型必选',
            'user_belong_type.in' =>'用户集体类型错误',
            'user_belong_id.required' =>'用户集体必选',
            'user_file.required' =>'用户excel名单丢失',
        ];
        //表单验证
        $validator = \Validator::make($request ->all(), [
            'user_belong_type' =>[
                'bail', 'required',
                Rule::in(array_keys(User::getBelongTypeMeaning()))
            ],
            'user_belong_id' =>'bail|required',
            'user_file' =>'bail|required'
        ], $message);
        if($validator ->fails()) {
            $pageData['error'] = true;
            $pageData['msg'] = $validator ->errors() ->all();
            return view('systemManage.usersManage.confirmUsersInfo') ->with($pageData);
        }
        //检查文件是否有效
        if(!$request ->hasFile('user_file') || !$request ->user_file ->isValid()) {
            $pageData['error'] = true;
            $pageData['msg'] = ['excel文件丢失'];
            return view('systemManage.usersManage.confirmUsersInfo') ->with($pageData);
        }
        //检查上传文件的格式是否正确
        $fileExtension = $request ->user_file ->getClientOriginalExtension();
        if(!in_array($fileExtension, config('excel.support_extension_list'))) {
            $pageData['error'] = true;
            $pageData['msg'] = ['上传文件只支持xls、xlsx两种格式'];
            return view('systemManage.usersManage.confirmUsersInfo') ->with($pageData);
        }
        //存储上传的文件
        $path = $request ->user_file ->store('temporary', 'public');
        $fullPath = storage_path('app/public') . '/' . $path;

        $excelData = [];

        //获取表格数据
        Excel::load($fullPath, function ($reader) use(&$excelData){
            $reader = $reader ->getSheet(0);
            $excelData = $reader ->toArray();
        });
        //表格数据判空
        if(count($excelData) <= 1) {
            $pageData['error'] = true;
            $pageData['msg'] = ['表格数据为空'];
            Storage::disk('public') ->delete($path);
            return view('systemManage.usersManage.confirmUsersInfo') ->with($pageData);
        }
        //获取归属信息
        $belong = User::getBelongsInfo($request ->user_belong_type, $request ->user_belong_id, ['id', 'name']);
        //组织用户信息
        $users = [];
        for($i = 1; $i < count($excelData); $i++) {
            $users[$i]['unique_id'] = intval($excelData[$i][0]);
            $users[$i]['name'] = $excelData[$i][1];
            $users[$i]['email'] = $excelData[$i][2];
            $users[$i]['mobile'] = $excelData[$i][3];
            $users[$i]['belong_type'] = intval($request ->user_belong_type);
            $users[$i]['belong_id'] = intval($request ->user_belong_id);
            $users[$i]['belong_name'] = intval($belong ->name);
        }
        $pageData['users'] = $users;
        //将文件中的数据保存在session中
        $request ->session() ->put($this ->excelSessionKey, serialize($users));
        Storage::disk('public') ->delete($path);

        $pageData['belongTypeMeaning'] = User::getBelongTypeMeaning();
        return view('systemManage.usersManage.confirmUsersInfo') ->with($pageData);
    }

    /**
     * 存储session中存着的user信息
     */
    public function saveUsersFromSession(Request $request) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        if($request ->session() ->has($this ->excelSessionKey) && !empty(unserialize($request ->session() ->get($this ->excelSessionKey)))) {
            $saveUsers = unserialize($request ->session() ->get($this ->excelSessionKey));
            $request ->session() ->forget($this ->excelSessionKey);
            //检查unique_id是否重复
            $userUniqueIds = array_column($saveUsers, 'unique_id');
            if(count(array_unique($userUniqueIds)) < count($userUniqueIds)) {
                $retData['error'] = true;
                $retData['msg'] = ['用户编号部分重复！'];
                return json_encode($retData);
            }
            if(User::whereIn('unique_id', $userUniqueIds) ->exists()) {
                $retData['error'] = true;
                $retData['msg'] = ['部分用户编号在数据库中已经存在!'];
                return json_encode($retData);
            }
            //检查邮箱格式是否正确
            $userEmails = array_column($saveUsers, 'email');
            foreach ($userEmails as $email) {
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $retData['error'] = true;
                    $retData['msg'] = ['存在用户邮箱格式错误！'];
                    return json_encode($retData);
                }
            }
            //检查用户手机号格式是否正确
            $userMobiles = array_column($saveUsers, 'mobile');
            foreach ($userMobiles as $mobile) {
                if(strlen($mobile) != 11) {
                    $retData['error'] = true;
                    $retData['msg'] = ['部分用户手机号格式错误！'];
                    return json_encode($retData);
                }
            }
            //批量存储数据
            foreach ($saveUsers as &$user) {
                $user['created_at'] = date('Y-m-d H:i:s', time());
                $user['updated_at'] = date('Y-m-d H:i:s', time());
                $user['creator_id'] = Auth::id();
                $user['updater_id'] = Auth::id();
                unset($user['belong_name']);
            }
            $userModel = new User;
            if($userModel ->insert($saveUsers)){
                return json_encode($retData);
            }
            else {
                $retData['error'] = true;
                $retData['msg'] = ['数据库操作失败'];
                return json_encode($retData);
            }
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['数据丢失，请重试！'];
            return json_encode($retData);
        }
    }

    /**
     * 重置密码
     */
    public function resetPassword($userId) {
        $user = new User;
        return json_encode($user ->changePasswordTo($userId, config('database.defaultUserPwd')));
    }

}
