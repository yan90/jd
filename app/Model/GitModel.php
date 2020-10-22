<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class GitModel extends Model
{
    //
    protected $table = "git";           //model使用的表
    protected $primaryKey = "uid";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
