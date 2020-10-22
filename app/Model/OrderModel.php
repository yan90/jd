<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table = "p_order_info";           //model使用的表
    protected $primaryKey = "order_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
