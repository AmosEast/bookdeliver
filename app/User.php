<?php

namespace App;

use App\Models\Academy;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolClass;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id', 'name', 'email', 'mobile', 'picture', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * belong_type字段定义
     */
    public static $belong_type_class = 1;
    public static $belong_type_academy = 2;

    public static function getBelongTypeMeaning() {
        return[
            self::$belong_type_class =>'班级',
            self::$belong_type_academy =>'学院'
        ];
    }
    /**
     * 获取用户的所有角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() {
        return $this ->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->withPivot('id', 'is_valid', 'creator_id', 'updater_id')
            ->withTimestamps()
            ->wherePivot('is_valid', '1');
    }

    /**
     * 判断用户是否拥有某一角色
     */
    public function hasRole($role) {
        return  $this ->roles ->contains('id', $role ->id);
    }

    /**
     * 判断用户是否拥有某一权限
     */
    public function hasPermission($permission) {
        if($permission) {
            $roles = $permission ->roles;
            if($roles) {
                foreach ($roles as $role) {
                    if($this ->hasRole($role)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 判断用户是否有超管角色
     */
    public function isSuperAdmin() {
        return $this ->roles() ->where('level', config('app.superAdminLevel')) ->exists();
    }

    /**
     * 获取用户归属信息
     */
    public static function getBelongsInfo($belongType, $belongId, $selects) {
        if($belongType && $belongId) {
            switch ($belongType) {
                case self::$belong_type_academy: {
                    return Academy::select($selects) ->where('id', $belongId) ->first();
                }
                case self::$belong_type_class: {
                    return SchoolClass::select($selects) ->where('id', $belongId) ->first();
                }
                default :{
                    return null;
                }
            }
        }
        else {
            return null;
        }
    }

    /**
     * 修改密码
     */
    public function changePasswordTo($userId, $newPassword) {
        $retData = [
            'error' =>false,
            'msg' =>[]
        ];
        if($userId) {
            if($user = User::find($userId)) {
                $user ->password = \Hash::make($newPassword);
                if($user ->save()) {
                    return $retData;
                }
                else {
                    $retData['error'] = true;
                    $retData['msg'] = ['数据库操作失败!'];
                    return $retData;
                }
            }
            else {
                $retData['error'] = true;
                $retData['msg'] = ['该用户不存在！'];
                return $retData;
            }
        }
        else {
            $retData['error'] = true;
            $retData['msg'] = ['用户id错误'];
            return $retData;
        }
    }
}
