<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PimModel extends Model
{
    //
    protected $table = "p_pim";           //model使用的表
    protected $primaryKey = "pim_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
