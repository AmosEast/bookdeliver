<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;

class SchoolClass extends BaseModel
{
    //
    protected $table = 'classes';

    protected $fillable = ['unique_id', 'name', 'description', 'major_id', 'grade', 'is_valid', 'creator_id', 'updater_id', 'created_at', 'updated_at'];

    /**
     * 获取相关的专业
     */
    public function major() {
        return $this ->belongsTo(Major::class, 'major_id', 'id');
    }

    /**
     * 获取相关的院系
     */
    public function academy() {
        return $this ->hasManyThrough(Academy::class, Major::class, 'id', 'id', 'major_id',  'academy_id');
    }

    /**
     * 获取该班级所有的学生
     */
    public function students() {
        return $this ->hasMany(User::class, 'belong_id', 'id')
            ->where([
                ['users.belong_type', '=', User::$belong_type_class],
                ['users.is_valid', '=', 1]
            ]);
    }

}
