<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PimModel;
use App\Model\UserModel;
class PimController extends Controller
{
    //
    public  function pim(){
        return view('index.pim');
    }
    public function pimm(){
        $user_id=session()->get('user_id');
        if(empty($user_id)){
            $data=[
                'erron'=>400001,
                'msg'=>'请先登录',
            ];
            return $data;
        }
        $time=strtotime(date('y-m-d'));
//        echo $time;exit;
        $data=[
            'user_id'=>$user_id,
            'pim_time'=>$time,
        ];

        $one=PimModel::where(['user_id'=>$user_id],['pim_time'=>$time])->first();
        if(!empty($one)){
            $data=[
                'erron'=>600001,
                'msg'=>'一天只能签到一次',
            ];
            return $data;
        }
        $res=PimModel::insert($data);
        if($res){
            $userInfo=UserModel::where('user_id',$user_id)->first();
            if(is_object($userInfo)){
                $userInfo=$userInfo->toArray();
            }
            $data=[
                'integral'=>$userInfo['integral']+100,
            ];
            UserModel::where('user_id',$user_id)->update($data);
//            dd($userInfo);
            $data=[
                'erron'=>200,
                'msg'=>'签到成功积分加100',
            ];
            return $data;
        }else{
            $data=[
                'erron'=>500001,
                'msg'=>'签到失败',
            ];
            return $data;
        }
    }
}

