<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Academy extends Model
{
    //
    protected $table = 'academies';

    protected $fillable = [
        'unique_id', 'name', 'description', 'is_valid'
    ];

    /**
     * 获取所有的专业
     */
    public function majors() {
        return $this ->hasMany(Major::class, 'academy_id', 'id');
    }

    /**
     * 获取所有的班级
     */
    public function schoolClasses() {
        return $this ->hasMany(SchoolClass::class, 'academy_id', 'id');
    }
}
