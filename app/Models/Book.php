<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Book extends BaseModel
{
    //
    protected $table = 'books';

    protected $fillable = ['isbn', 'name', 'description', 'publishing', 'price', 'discount', 'type', 'course_id'];

    /**
     * 书籍类型定义
     */
    public static $bookForStudent = 1;
    public static $bookForTeacher = 2;

    public static function getBookTypeMeaning() {
        return [
            self::$bookForStudent =>'教科书类',
            self::$bookForTeacher =>'教参书类'
        ];
    }


    /**
     * 获取该书籍的课程
     */
    public function course() {
        return $this ->belongsTo(Course::class, 'course_id', 'id');
    }

    /**
     * 获取该书籍的所有订单
     */
    public function orders() {
        return $this ->hasMany(Order::class, 'book_id', 'id')
            ->where('orders.is_valid', '=', true);
    }
}
