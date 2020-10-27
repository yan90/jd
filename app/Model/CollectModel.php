<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class CollectModel extends Model
{
    protected $table = "p_collect";           //model使用的表
    protected $primaryKey = "collect_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
