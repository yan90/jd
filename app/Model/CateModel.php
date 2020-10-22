<?php

namespace App\Model;

use Encore\Admin\Traits\ModelTree;

use Illuminate\Database\Eloquent\Model;

class CateModel extends Model
{
    //
    use ModelTree;
    protected $table = "p_category";           //model使用的表
    protected $primaryKey = "cat_id";  //主键
    public $timestamps = false;
    protected $guarded = [];
    public function __construct(array $attributes=[])
    {
        parent::__construct($attributes);
        
      
        $this->setParentColumn('parent_id');
        $this->setOrderColumn('sort_order');
        $this->setTitleColumn('cat_name');
    }
}



