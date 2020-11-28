<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\GoodsModel;
use App\Model\XcxcartModel;
use App\model\XcxuserModel;
class XcxController extends Controller
{
    public function login(Request $request){
//        echo '<pre>';print_r($_POST);echo '</pre>';
//        echo '<pre>';print_r($_GET);echo '</pre>';
        //接收code
        $code=$request->get('code');
        //使用code
        //获取用户信息
        $userinfo=json_decode(file_get_contents("php://input"),true);
        $res='https://api.weixin.qq.com/sns/jscode2session?appid='.env('WX_XCX_APPID').'&secret='.env('WX_XCX_SECRET').'&js_code='.$code.'&grant_type=authorization_code';
//       echo $res;exit;
        $data=json_decode(file_get_contents($res),true);
//        echo '<pre>';print_r($data);echo '</pre>';
        //自定义登录状态
        if(isset($data['errcode'])){  //有错误
            $response=[
                'errno'=>500001,
                'msg'=>'登录失败',
                ];
        }else{          //成功
            $openid=$data['openid'];
            $u=XcxuserModel::where(['openid'=>$openid])->first();
            if($u){
                //TODO 老用户
            }else{
                $u_info=[
                    'openid'=>$openid,
                    'nickname'=>$userinfo['u']['nickName'],
                    'sex'=>$userinfo['u']['gender'],
                    'language'=>$userinfo['u']['language'],
                    'city'=>$userinfo['u']['city'],
                    'province'=>$userinfo['u']['province'],
                    'country'=>$userinfo['u']['country'],
                    'headimgurl'=>$userinfo['u']['avatarUrl'],
                    'add_time'=>time(),
//                    'type'=>3
                ];
                XcxuserModel::insertGetId($u_info);
            }
//            $xcxModel=XcxModel::insert(['openid'=>$data['openid']]);
            $token=sha1($data['openid'].$data['session_key'].mt_rand(0,999999));
           //保存token
            $redis_key='xcx_token:'.$token;
            $loginInfo=[
                'uid'=>'1234',
                'user_name'=>'李明',
                'login_time'=>time(),
                'login_ip'=>$request->getClientIp(),
                'token'=>$token,
                'openid'=>$openid
            ];
            Redis::hMset($redis_key,$loginInfo);
            //设置过期时间
            Redis::expire($redis_key,7200);
            $response=[
                'errno'=>0,
                'msg'=>'登录成功',
                'data'=>[
                    'token'=>$token
                ]
            ];
        }
        return $response;
    }
    //小程序首页
    public function circulation(){
    $goods=GoodsModel::inRandomOrder()->take(10)->get()->toArray();
//    echo '<pre>';print_r($goods);echo '</pre>';exit;
        return json_encode($goods,256);

    }
    //加入购物车
    public function cart(Request $request){
        $goods_id=$request->get('goods_id');
        //获取token
        $token=$request->get('token');
//        dd($token);
//        dd($token);
        $key="xcx_token:".$token;
//        dd($key);
        //取出openid
        $token=Redis::hgetall($key);
//        dd($token);
        $user_id=XcxuserModel::where('openid',$token['openid'])->select('id')->first();
//        dd($user_id);

        $data=[
            'goods_id'=>$goods_id,
            'add_time'=>time(),
            'user_id'=>$user_id->id,
        ];
       $res= XcxcartModel::insert($data);
       if($res){
           $respones=[
               'error'=>0,
               'msg'=>'加入购物车成功',
           ];
       }else{
           $respones=[
               'error'=>50001,
               'msg'=>'加入失败',
           ];
       }
        return $respones;
    }
    //购物车列表
    public function cartlist(Request $request){

        $token=$request->get('token');
        $key="xcx_token:".$token;
        //取出openid
        $token=Redis::hgetall($key);
        $user_id=XcxuserModel::where('openid',$token['openid'])->select('id')->first();
//        echo ($user_id);exit;
        $goods = XcxcartModel::where(['user_id'=>$user_id->id])->get();
        if($goods)      //购物车有商品
        {
            $goods = $goods->toArray();
            foreach($goods as $k=>&$v)
            {
                $g = GoodsModel::find($v['goods_id']);
//                dd($g);
                $v['goods_name'] = $g->goods_name;
                $v['shop_price'] = $g->shop_price;
                $v['goods_img'] = $g->goods_img;
            }
        }else{          //购物车无商品
            $goods = [];
        }
        //echo '<pre>';print_r($goods);echo '</pre>';die;
        $response = [
            'errno' => 0,
            'msg'   => 'ok',
            'data'  => [
                'list'  => $goods
            ]
        ];

        return $response;
    }
    //全部删除
    public function alldelete(Request $request){
        $token=$request->get('token');
//        dd($token);
        $key="xcx_token:".$token;
        //取出openid
        $token=Redis::hgetall($key);
        $user_id=XcxuserModel::where('openid',$token['openid'])->select('id')->first();
    }
}
