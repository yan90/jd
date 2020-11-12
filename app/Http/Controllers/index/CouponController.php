<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\CouponModel;
class CouponController extends Controller
{
    //领劵页面
    public function index(){

        return view('coupon.index');
    }
    //领劵逻辑
    public function getcoupon(Request $request){
        //根据type领不同的劵
        $type=$request->get('type');
        switch ($type){
            case 1:                  //满一百减20
                $this->coupon100_20();
                break;
            case 2:
                $this->coupon200_40();    //满200-40
                break;
            case 3:
                echo "333333333333";
                break;
            default:
                echo "参数错误";
                break;
        }
        //领劵
        $response=[
            'errno'=>0,
            'msg'=>'领劵成功',
        ];
        return $response;
    }
    //生成 满100-20
    private function coupon100_20(){
    $user_id=session()->get('user_id');
    $begin_time=strtotime("2020-11-11");
    $expire_time=strtotime("2020-11-12");
    $data=[
        'user_id'=>$user_id,
        'add_time'=>time(),
        'begin_time'=>$begin_time,
        'expire_time'=>$expire_time,
        'type'=>1,      //type=1 满100-20
    ];
    CouponModel::insertGetId($data);
    }
}
