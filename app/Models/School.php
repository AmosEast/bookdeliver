<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

class School extends BaseModel
{
    //
    protected $table = 'schools';
    protected $fillable = ['unique_id', 'name', 'description', 'is_valid', 'creator_id', 'updater_id'];
}
