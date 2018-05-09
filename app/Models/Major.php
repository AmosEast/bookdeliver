<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Major extends BaseModel
{
    //
    protected $table = 'majors';

    protected $fillable = ['unique_id', 'name', 'description', 'academy_id', 'is_valid', 'creator_id', 'updater_id'];


    /**
     * 获取所有的班级
     */
    public function schoolClasses() {
        return $this ->hasMany(SchoolClass::class, 'major_id', 'id');
    }

    /**
     * 获取相关的学院
     */
    public function academy() {
        return $this ->belongsTo(Academy::class, 'academy_id', 'id');
    }

    /**
     * 获取该专业所有的课程
     */
    public function courses() {
        return $this ->belongsToMany(Course::class, 'major_courses', 'major_id', 'course_id')
            ->where('courses.is_valid', '=', 1)
            ->withPivot('id', 'is_valid', 'creator_id', 'updater_id')
            ->withTimestamps()
            ->wherePivot('is_valid', '=', 1);
    }

}
