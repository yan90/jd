<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use App\Model\OrderGoodsModel;
use App\Model\OrderModel;
use Illuminate\Http\Request;
use App\Model\CartModel;
use Illuminate\Support\Str;
class OrderController extends AlipayController
{
    //订单页面
    public function order(Request $request){
            //TODO  获取购物车中的商品  （根据当前用户id）
            $user_id=session()->get('user_id');
            $cart_goods=CartModel::where(['user_id'=>$user_id])->get();
//            dd($cart_goods->toArray());
            //空购物车
            if(empty($cart_goods)){

            }
            //TODO 生成订单号   计算订单总价  记录订单信息  （订单表orders）
        $total=0;
        foreach ($cart_goods as $k=>$v)
        {
            $goods_price=$v['goods_price'];
            $total +=$goods_price;
            $v['goods_price']=$goods_price;
            $order_goods[]=$v;
        }
        $order_data=[
            'order_sn'=>strtolower(Str::random(20)),//订单唯一编号
            'user_id'=>$user_id,
            'order_amount'=>$total,
            'add_time'=>time(),

        ];
        $oid=OrderModel::insertGetId($order_data);     //订单入库
        //记录订单商品  （订单商品表orders_goods）
        foreach ($cart_goods as $k=>$v){
            $goods=[
              'order_id'=>$oid,
              'goods_id'=>$v['goods_id'],
              'goods_name'=>$v['goods_name'],
              'goods_price'=>$v['goods_price'],
            ];
            OrderGoodsModel::insertGetId($goods);
        }
       //清空购物车
        CartModel::where(['user_id'=>$user_id])->delete();
        //跳转至  支付页面
        $order = [
            'out_trade_no' => $order_data['order_sn'],
            'total_amount' =>$total ,
            'subject' => '购物车',
        ];
        return $this->Alipay($order);
    }





}
