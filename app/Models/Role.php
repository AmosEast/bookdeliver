<?php

namespace App\Models;

use App\User;
use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Role extends BaseModel
{
    protected $fillable = [
        'name', 'description', 'created_at', 'updated_at'
    ];


    /**
     * 获取同一角色下的所有用户
     */
    public function users() {
        return $this ->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
                      ->withPivot('id', 'is_valid', 'creator_id', 'updater_id')
                      ->withTimestamps()
                      ->wherePivot('is_valid', '1');
    }

    /**
     * 获取同一角色的所有权限
     */
    public function permissions() {
        return $this ->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
                      ->withPivot('id', 'is_valid', 'creator_id', 'updater_id')
                      ->withTimestamps()
                      ->wherePivot('is_valid', '1');
    }
}
