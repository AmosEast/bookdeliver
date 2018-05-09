<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Order extends BaseModel
{
    //
    protected $table = 'orders';
    protected $fillable = ['task_id', 'select_id', 'user_id', 'book_id', 'quantity', 'deliver_sign', 'receiver_id', 'received_ext', 'creator_id', 'updater_id'];

    /**
     * 获取订单包含的书籍信息
     */
    public function book() {
        return $this ->belongsTo(Book::class, 'book_id', 'id');
    }

}
