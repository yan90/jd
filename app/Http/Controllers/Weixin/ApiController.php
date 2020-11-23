<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
use Illuminate\Http\Request;

class ApiController extends Controller
{
//    public function __construct()
//    {
//        app('debugbar')->disable();     //关闭调试
//    }
        //测试
    public function test(){
//        echo '<pre>';print_r($_POST);echo '</pre>';
//        echo '<pre>';print_r($_GET);echo '</pre>';
        $goods_info=[
          'goods_id'=>13345,
            'goods_name'=>"iphone",
            'price'=>5000
        ];
        return $goods_info;
//        echo json_encode(.5kg$goods_info);
    }
    //小程序详情页接收id
    public function goods_details(Request $request){
        $token=$request->get('access_token');
        //验证token是否有效
        echo $token;
    $goods_id=$request->get('goods_id');
//    echo $goods_id;exit;
    $detail=GoodsModel::where('goods_id',$goods_id)->first()->toArray();
    return $detail;
    }
    //下拉刷新
    public function goodsList(Request $request){

        $page_size=$request->get('ps');
//        echo $page_size;exit;
        $g=GoodsModel::select('goods_id','goods_name','shop_price','goods_img')->paginate($page_size);
//        echo '<pre>';print_r($g);echo '</pre>';
        $response=[
            'errno'=>0,
            'msg'=>'ok',
            'data'=>[
                'list'=>$g->items()
            ]
        ];
        return $response;
    }
}
