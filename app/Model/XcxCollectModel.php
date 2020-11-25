<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class XcxCollectModel extends Model
{
    protected $table = "p_xcx_collect";           //model使用的表
//    protected $primaryKey = "goods_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
