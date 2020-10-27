<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PrizeModel extends Model
{
    protected $table = "p_prize";           //model使用的表
//    protected $primaryKey = "goods_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
