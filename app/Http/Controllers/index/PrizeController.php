<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PrizeModel;
class PrizeController extends Controller
{
    //抽奖
    public  function index(){
        return view('prize.prize');
    }
    /**
     *开始抽奖
     */
    public  function  add(){
        $user_id=session('user_id');
        if(empty($user_id)){
            $respose=[
                'errno'=>400,
                'msg'=>'请先登录',
            ];
            return  $respose;
        }
        //检查用户当天是否有抽奖记录
        $time1=strtotime(date("Y-m-d"));
        $res=PrizeModel::where(['user_id'=>$user_id])->where("add_time",">=",$time1)->first();
        if($res){
            $respose=[
                'errno'=>30008,
                'msg'=>'今天以抽奖了，请明天再来哦亲',
            ];
            return  $respose;
        }
    $rand=mt_rand(1,1000);
        $prize="很抱歉 期待您下次中奖";
    if($rand>=1&&$rand<=10){
        //一等奖
        $prize="恭喜获得一等奖";
    }elseif ($rand>=11 &&$rand<=30){
        //二等奖
        $prize="恭喜获得二等奖";
    }elseif($rand>=31&&$rand<=60){
        //三等奖
        $prize="恭喜获得三等奖";
    }
    //记录抽奖信息
        $prize_data=[
          'user_id'=>$user_id,
          'level'=>$prize,
          'add_time'=>time(),
        ];
    $pid=PrizeModel::insertGetId($prize_data);

    //是否入库成功
if($pid>0){
    $data=[
        'errno'=>0,
        'msg'=>'ok',
        'data'=>[
            'prize'=>$prize
        ]
            ];
        }else{
            $data=[
              'errno'=>50008,
              'msg'=>"数据异常，请重试",
            ];
        }

    return $data;
    }


}
