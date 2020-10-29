<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class HistoryModel extends Model
{
    protected $table = "p_history";           //model使用的表
    public $timestamps = false;
    protected $guarded = [];
}
