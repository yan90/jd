<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    //
    protected $table = "p_cart";           //model使用的表
    protected $primaryKey = "goods_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
