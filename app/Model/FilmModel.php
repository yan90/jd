<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class FilmModel extends Model
{
    //

    // 表名
    protected $table = 'p_user_film';
    protected $primaryKey = "film_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
}
