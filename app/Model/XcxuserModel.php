<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class XcxuserModel extends Model
{
    protected $table = "p_xcx_user";           //model使用的表
//    protected $primaryKey = "goods_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
