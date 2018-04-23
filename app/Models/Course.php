<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Course extends BaseModel
{
    //
    protected $table = 'courses';

    protected $fillable = ['name', 'description', 'type', 'creator_id', 'updater_id'];

//    //课程类型
//    public static $gradeFirst = 1;
//    public static $gradeSecond = 2;
//    public static $gradeThird = 3;
//    public static $gradeForth = 4;
//
//    /**
//     * 课程类型含义
//     */
//    public static function getCourseTypeMeaning() {
//        return [
//            self::$gradeFirst =>'大学一年级',
//            self::$gradeSecond =>'大学二年级',
//            self::$gradeThird =>'大学三年级',
//            self::$gradeForth =>'大学四年级',
//        ];
//    }


    /**
     * 获取该课程所属的专业
     */
    public function majors() {
        return $this ->belongsToMany(Major::class, 'major_courses', 'course_id', 'major_id')
            ->withPivot('id', 'is_valid', 'creator_id', 'updater_id')
            ->withTimestamps()
            ->wherePivot('is_valid', '=', 1);
    }

}
