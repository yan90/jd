<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{
    //首页
    public function index(){
       
        return view('index/index');
    }
    //秒杀
    public function seckill(){
        return view('index/seckill');
    }
    //购物车
    public function cart(){
        return view('index/cart');
    }
    //商品详情
    public function particulars (Request $request){
        $goods_id=$request->get('id');
        $key='h:goods_info:'.$goods_id;

        //查询缓存
        $g=Redis::HGetALL($key); 
        if($g){//有缓存
             echo '有缓存,不用查询数据库';

        }else{
            echo '无缓存,正在查询数据库';
             //获取商品信息
        $goods_info=GoodsModel::find($goods_id);
        if(empty($goods_info)){
            echo '商品不存在';
            exit;
        }
        $g=$goods_info->toArray();

        //存入缓存
        Redis::hmset($key,$g);
        echo '数据存在redis中';exit;
        } 
        echo '<pre>';print_r($g);echo '</pre>';exit;
        $data=[
            'goods'=>$g
        ];
        return view('index/particulars',$data);
    }
}
