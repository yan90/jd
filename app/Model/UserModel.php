<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = "p_users";           //model使用的表
    protected $primaryKey = "user_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
