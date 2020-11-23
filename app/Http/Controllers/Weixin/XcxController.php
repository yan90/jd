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
            Redis::set($redis_key,time());
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

        $user_id=XcxuserModel::value('id');
        if(empty($user_id)){
            $data=[
                'error'=>50002,
                'msg'=>'请先登录',
            ];
            return $data;
        }
//        dd($user_id);
        $goods_id=$request->get('goods_id');
//        dd($goods_id);
        $data=[
            'goods_id'=>$goods_id,
            'add_time'=>time(),
            'user_id'=>$user_id,
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
}