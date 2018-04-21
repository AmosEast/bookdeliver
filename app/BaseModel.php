<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    //

    /**
     * 获取创建者
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() {
        return $this ->belongsTo(User::class, 'creator_id');
    }

    /**
     * 获取更新者
     */
    public function updater() {
        return $this ->belongsTo(User::class, 'updater_id');
    }
}
