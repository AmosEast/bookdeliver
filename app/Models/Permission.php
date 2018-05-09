<?php

namespace App\Models;

use App\BaseModel;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class Permission extends BaseModel
{
    public $fillable = [
        'name', 'description', 'controller', 'function', 'is_valid', 'creator_id', 'updater_id'
    ];

    /**
     * 获取拥有该权限的所有角色
     */
    public function roles() {
        return $this ->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
                      ->where('roles.is_valid', '=', 1)
                      ->withPivot('id', 'is_valid', 'creator_id', 'updater_id')
                      ->withTimestamps()
                      ->wherePivot('is_valid', '1');
    }
}
