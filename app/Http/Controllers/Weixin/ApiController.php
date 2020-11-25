<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\XcxCollectModel;
use App\model\XcxuserModel;
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
    //收藏
    public function add_fav(Request $request){
        $goods_id=$request->get('id');
//        $token=$request->get('token');
//        echo $token;
        //加入收藏放入redis有序集合
//        $user_id=8;
//        $redis_key='ss:goods:fav:.user_id'; //用户收藏有序集合
//        Redis::Zadd($redis_key,time(),$goods_id); //将商品id加入有序值，并给排序值
        $token=$request->get('token');
        $key="xcx_token:".$token;
        //取出openid
        $token=Redis::hgetall($key);
        $user_id=XcxuserModel::where('openid',$token['openid'])->select('id')->first();
        $data=[
            'goods_id'=>$goods_id,
            'add_time'=>time(),
            'user_id'=>$user_id->id
        ];
        $res=XcxCollectModel::insert($data);

        if($res){
            $respones=[
                'error'=>0,
                'msg'=>'收藏成功',
            ];
        }else{
            $respones=[
                'error'=>50001,
                'msg'=>'收藏失败',
            ];
        }
        return $respones;
//        $response=[
//            'errno'=>0,
//            'msg'=>'收藏成功',
//        ];
//        return $response;
    }
    //取消收藏
    public function no_fav(Request $request){
        $goods_id=$request->get('id');
        $token=$request->get('token');
        $key="xcx_token:".$token;
        $token=Redis::hgetall($key);
//        echo $token;exit;
        $user_id=XcxuserModel::where('openid',$token['openid'])->value('id');
        $res=XcxCollectModel::where(['user_id'=>$user_id,'goods_id'=>$goods_id])->delete();
        if($res){
            $respones=[
                'error'=>0,
                'msg'=>'取消收藏成功',
            ];
        }else{
            $respones=[
                'error'=>50001,
                'msg'=>'取消收藏失败',
            ];
        }
        return $respones;

    }
}
