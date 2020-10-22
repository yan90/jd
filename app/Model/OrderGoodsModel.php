<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderGoodsModel extends Model
{
    protected $table = "p_order_goods";           //model使用的表
    protected $primaryKey = "rec_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
