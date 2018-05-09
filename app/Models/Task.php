<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Task extends BaseModel
{
    //
    protected $table = 'tasks';

    protected $fillable = ['name', 'description', 'creator_id', 'updater_id'];

    /**
     * 任务状态定义
     */
    public static $create_complete = 0;
    public static $select_process = 1;
    public static $select_complete = 2;
    public static $order_process = 3;
    public static $order_complete = 4;
    public static $deliver_process = 5;
    public static $deliver_complete = 6;

    public static function getTaskStatusMeanings() {
        return [
            self::$create_complete =>'任务创建完成',
            self::$select_process =>'教师选书过程中',
            self::$select_complete =>'教师选书完成',
            self::$order_process =>'书籍预订过程中',
            self::$order_complete =>'书籍预订完成',
            self::$deliver_process =>'书籍发放过程中',
            self::$deliver_complete =>'任务结束'
        ];
    }

    /**
     * 任务阶段切换时提示信息
     */
    public static function getTipsForStatus() {
        return [
            self::$select_process =>'在该阶段中，将不能编辑任务信息，同时将通知各院管理员进行教师选书任务。',
            self::$select_complete =>'在该阶段中，将禁止教师修改选书信息，请确认所有书籍选择已通过审核。',
            self::$order_process =>'在该阶段中，将进行学生以及教师选购教材以及教参类书籍，请及时通知各院进行书籍选购。',
            self::$order_complete =>'在该阶段中，将禁止学生以及教师修改订购书籍信息，请确认用户购书信息不需要修改。',
            self::$deliver_process =>'在该阶段中，将进行书籍发放工作，请及时通知各院领取书籍。',
            self::$deliver_complete =>'该阶段表示此次选书以及发放任务完成，请确认！'
        ];
    }

    /**
     * 获取当前任务的所有选书安排
     */
    public function selectLists() {
        return $this ->hasMany(SelectList::class, 'task_id', 'id')
            ->where('select_lists.is_valid', '=', 1);
    }
}
