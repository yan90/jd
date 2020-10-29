<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class SeatModel extends Model
{

    // 表名
    protected $table = 'p_user_seat';
    // 主键
    protected $primaryKey = 'seat_id';
    // 添加允许为空 的 created_at 和 updated_at TIMES TAMP 类型列
    public $timestamps = false;
    // 黑名单
    protected $guarded = [];
}
