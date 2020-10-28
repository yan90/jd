<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use App\model\CollectModel;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Model\CartModel;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp \Client;
class IndexController extends Controller
{
    //首页
    public function index(){
        //右侧收藏列表
        $collectInfo=CollectModel::get();
        //猜你喜欢
        $goodsInfo=GoodsModel::limit(6)->get()->toArray();

        return view('index/index',['collectInfo'=>$collectInfo,'goodsInfo'=>$goodsInfo]);
    }
    //秒杀
    public function seckill(){
        return view('index/seckill');
    }
    //加入购物车
    public function cart(Request $request){
        // $goods=GoodsModel::first('goods_name');
        $goods=new GoodsModel();
        $goodsinfo=$goods->first();
        //  print_r($goodsinfo);exit;
        $user_id=session()->get('user_id');
        if(empty($user_id)){
            $data=[
                'errno'=>50001,
                'msg'=>'请先登录',
            ];
            echo json_encode($data);
            die;
        }
        $goods_id=$request->get('id');
        $goods_num=$request->get('goods_num',1);
        $goods_name=$goodsinfo['goods_name'];
        $shop_price=$goodsinfo['shop_price'];
        $goods_img=$goodsinfo['goods_img'];


        //检查是否下架   库存是否充足...
        //购物车保存商品信息
        $cart_info=[
            'goods_id'=>$goods_id,
            'user_id'=>$user_id,
            'goods_num'=>$goods_num,
            'add_time'=>time(),
            'goods_name'=>$goods_name,
            'goods_price'=>$shop_price,
            'goods_img'=>$goods_img,
        ];
        //  dd($cart_info);
        $res=CartModel::insertGetId($cart_info);
        // dd($res);
        if($res>0){
            $data=[
                'errno'=>0,
                'msg'=>'成功加入购物车',
            ];
            echo json_encode($data);
        }else{
            $data=[
                'errno'=>50001,
                'msg'=>'加入购物车失败',
            ];
            echo json_encode($data);
        }



    }
    //购物车页面
    public function index_cart(){
        $user_id=session()->get('user_id');
        if(empty($user_id)){
            return redirect('index/login');
        }
            // 取购物车的商品信息
            $list=CartModel::where(['user_id'=>$user_id])->get();
            // foreach($list as $k=>$v){
            //     $goods[]=GoodsModel::find($v['goods_id'])->toArray();
            // }
            // $data=[
            //     'goods'=>$goods
            // ];
            // dd($list);
            //总价格
        $sum=CartModel::sum('goods_price');
        return view('index/cart',['list'=>$list,'sum'=>$sum]);

    }
    //商品详情
    public function particulars (Request $request){
        $user_id=session('user_id');
        $goods_id=$request->get('id');
         $key='h:goods_info:'.$goods_id;
        //查询缓存
         $g=Redis::HGetALL($key);
         if($g){//有缓存
              echo '有缓存,不用查询数据库';
         }else {
             echo '无缓存,正在查询数据库';
         }
        //      //获取商品信息
        $goods_info=GoodsModel::find($goods_id);

        //验证商品是否有效（是否存在  是否下架  是否删除）
        if(empty($goods_info)){
            echo '商品不存在';
            exit;
        }
        if($goods_info->is_delete==1){
            echo '商品已删除';
            exit;
        }

        $g=$goods_info->toArray();
         //存入缓存
         Redis::hmset($key,$g);
        // echo '数据存在redis中';exit;
        // }
        // echo '<pre>';print_r($g);echo '</pre>';
        $data=[
            'goods'=>$g
        ];
//          print_r($data);exit;
        //查询用户是否收藏改课程
        $where=[
          ['user_id','=',$user_id]  ,
            ['goods_id','=',$goods_id],
        ];
        $collect=CollectModel::where($where)->first();
        if(!empty($collect)){
            $collect=1;
        }else{
            $collect=2;
        }
        //记录浏览排行+1
        GoodsModel::where(['goods_id'=>$goods_id])->increment('click_count');
        return view('index/particulars',$data,['collect'=>$collect]);
    }
    //查询天气
    public function weather(){
        $uri='https://devapi.qweather.com/v7/weather/now?location=101010700&key=0e48ddc1234143b99e8a741c550f49c4&gzip=n';
        $json_str=file_get_contents($uri);
        $data=json_decode($json_str,true);
       echo  '<pre>';print_r($data);echo '</pre>';
    }
    //天气的curl方法
    public function weather2(){
        $uri='https://devapi.qweather.com/v7/weather/now?location=101010700&key=0e48ddc1234143b99e8a741c550f49c4&gzip=n';
                        // 创建一个新cURL资源
                    $ch = curl_init();
                    // 设置URL和相应的选项
                    curl_setopt($ch, CURLOPT_URL, $uri);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    //关闭HTTPS
                    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
                    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
                    // 抓取URL并把它传递给浏览器
                    $json_str=curl_exec($ch);
                    //捕获报错
                    $err_no=curl_errno($ch);
                    if($err_no){
                        $err_msg=curl_error($ch);
                        echo '错误信息：'.$err_msg;
                        exit;
                    }
                    // 关闭cURL资源，并且释放系统资源
                    curl_close($ch);
                    $data=json_decode($json_str,true);
                    echo '<pre>';print_r($data);echo '</pre>' ;
                        }
    //curlPOST方法
    public function post(){
        $url='https://devapi.qweather.com/v7/weather/now?location=101010700&key=0e48ddc1234143b99e8a741c550f49c4&gzip=n/post';
        $client=new Client();
        $res=$client->request('POST',$url,['verify'=>false]);
        $body = $res->getBody(); //获取接口相应的数据
        $data=json_decode($body,true);
        print_r($data);
            echo $body;
        }
        //cur获取天气Guzzle方法
    public function Guzzle(){
        $url='https://devapi.qweather.com/v7/weather/now?location=101010700&key=0e48ddc1234143b99e8a741c550f49c4&gzip=n';
        $client=new Client();
        $res=$client->request('GET',$url,['verify'=>false]);
        $body = $res->getBody(); //获取接口相应的数据
        $data=json_decode($body,true);
        print_r($data);
            echo $body;
}
        //购物车里面的删除
    public function delete($goods_id){
            $goods_id=request()->goods_id??'';
            $res=CartModel::destroy($goods_id);
            // dd($res);
            if($res){
                return redirect('/index/index_cart');
            }
        }
    //收藏
    public  function  fav(Request $request){
        $id = $request->id;
        $user_id = session('user_id');
        if(empty($user_id)){
            $data = [//没有登录
                'erron' =>400,
                'msg' =>'请先登录',
            ];
        }
        // 收藏表
        $where = [
            'user_id'  =>$user_id,
        ];
        $data = [
            'goods_id'  =>$id,
            'user_id'   =>$user_id,
            'collect_time'  =>time(),
        ];
        //收藏+1
        GoodsModel::where(['goods_id'=>$id])->increment('fav_count');
        $res = CollectModel::where($where)->first();
        if(empty($res)){
            CollectModel::insert($data);
            $data = [
                'erron' =>200,
                'msg'   =>'收藏成功',
            ];

            return json_encode($data,true);
        }else {
            CollectModel::where($where)->delete();
            $data = [
                'erron' => 201,
                'msg' => '取消收藏成功',
            ];

            return json_encode($data, true);
        }

    }
}
