<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $fillable = [
        'name', 'description', 'controller', 'function', 'is_valid', 'creator_id', 'updater_id'
    ];
}
