<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class GithubloginModel extends Model
{
    //
    protected $table = "github";           //model使用的表
    protected $primaryKey = "id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
