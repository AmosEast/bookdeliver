<?php

namespace App\Models;

use App\BaseModel;
use App\User;
use Illuminate\Database\Eloquent\Model;

class SelectList extends BaseModel
{
    //
    protected $table = 'select_lists';

    protected $fillable = ['task_id', 'academy_id', 'major_id', 'grade', 'course_id', 'selector_id', 'book_ids', 'status'];

    /**
     * 状态定义
     */
    public static $noSubmit = 10;
    public static $hasSubmit = 11;
    public static $finalRefused = 20;
    public static $finalAgreed = 21;

    public static function getStatusMeaning() {
        return [
            self::$noSubmit =>'未提交审核',
            self::$hasSubmit =>'已提交审核',
            self::$finalAgreed =>'教材科审核通过',
            self::$finalRefused =>'教材科审核拒绝'
        ];
    }

    /**
     * 获取可修改书籍选择的状态
     */
    public static function getStatusforEdit() {
        return [self::$noSubmit];
    }
    /**
     * 获取不可修改书籍选择的状态
     */
    public static function getStatusForNoEdit() {
        return [self::$hasSubmit, self::$finalAgreed, self::$finalRefused];
    }

    /**
     * 获取该选书安排的任务信息
     */
    public function task() {
        return $this ->belongsTo(Task::class, 'task_id', 'id');
    }

    /**
     * 获取该选书安排的选书人信息
     */
    public function selector() {
        return $this ->belongsTo(User::class, 'selector_id', 'id');
    }

    /**
     * 获取该选书安排的课程
     */
    public function course() {
        return $this ->belongsTo(Course::class, 'course_id', 'id');
    }

}
