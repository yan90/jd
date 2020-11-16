<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function test(){
//        echo '<pre>';print_r($_POST);echo '</pre>';
//        echo '<pre>';print_r($_GET);echo '</pre>';
        $goods_info=[
          'goods_id'=>13345,
            'goods_name'=>"iphone",
            'price'=>5000
        ];
        return $goods_info;
//        echo json_encode($goods_info);
    }
}
