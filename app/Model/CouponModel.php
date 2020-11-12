<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CouponModel extends Model
{
    //领劵表
    protected $table = "p_coupons";           //model使用的表
    protected $primaryKey = "id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
