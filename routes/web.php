<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});,klkml,l,l
Route::get('/register','index\LoginController@register'); //        //前台注册视图
Route::post('/index/home','index\LoginController@home');              //注册执行
Route::get('/user/active','index\LoginController@active');             //激活用户
Route::get('/login','index\LoginController@login');             //前台登录视图
Route::post('/index/logindo','index\LoginController@logindo');           //登录执行
Route::get('/logout','index\LoginController@logout');                //退出
Route::get('/','index\IndexController@index');                 //前台首页
Route::get('/index/seckill','index\IndexController@seckill');        //秒杀
Route::get('/index/cart','index\IndexController@cart')->middleware('check.login');                    //加入购物车
Route::get('/index/fav','index\IndexController@fav');                    //收藏
Route::get('/index/index_cart','index\IndexController@index_cart');        //购物车视图
Route::get('/index/particulars','index\IndexController@particulars');        //商品详情
Route::get('/index/order','index\OrderController@order');                  //订单页面
Route::get('/index/pay','index\OrderController@pay');                           //提交订单
Route::get('/index/weather','index\IndexController@weather');                 //天气
Route::get('/index/weather2','index\IndexController@weather2');           //cur获取天气
Route::get('/index/post','index\IndexController@post');           //cur获取天气POSTf方法
Route::get('/index/Guzzle','index\IndexController@Guzzle');           //cur获取天气Guzzle方法
Route::get('/success','Index\LoginController@success');               //第三方登录
Route::get('github/callback','Index\LoginController@callback');
Route::get('/index/delete/{goods_id}','index\IndexController@delete');        //购物车里面的删除
Route::get('/index/email','index\LoginController@email');           //登录执行
Route::get('/index/enroll','index\LoginController@enroll');           //注册成功后跳转的注册成功视图

Route::get('alipay','index\AlipayController@Alipay');  // 发起支付请求
Route::any('notify','index\AlipayController@AliPayReturn'); //服务器异步通知页面路径
Route::any('return','index\AlipayController@AliPayNotify');  //页面跳转同步通知页面路径
Route::any('prize','index\PrizeController@index');  //抽奖
Route::any('prize/add','index\PrizeController@add');  //开始抽奖
Route::get('film','MovieController@film');  //电影

Route::post('film/add','MovieController@filmadd');// 电影购票

Route::get('pim','index\PimController@pim');// 个人中心
Route::get('user/pim','index\PimController@pimm');// 个人中心

Route::get('/coupon','index\CouponController@index');// 领劵页面
Route::get('/coupon/get','index\CouponController@getcoupon')->middleware('check.login');// 领劵
Route::post('/coupon/test','index\CouponController@test');// 领劵页面
//微信
Route::post('/wx','TextController@checkSignature');  //接口微信
Route::get('/wx/token','TextController@token');  //access_token
//Route::get('/tell','TextController@tell');  //postman测试
//Route::post('/tell2','TextController@tell2');  //postman测试
Route::get('/custom','TextController@custom');  //自定义菜单
//微信授权页面
Route::get('/web_auth','TextController@wxWebAuth');
//微信重定向跳转地址
Route::get('/web_redirect','TextController@wxWebRedirect');
Route::get('/set_label',"TextController@set_label");  //创建标/5///  签
//TEST 路由分组
//Route::prefix('/text')get()->group(function (){
//
//});
Route::get('getweather','TextController@getweather');
Route::get('/guzzle',"TextController@guzzle");  //guzzle 测试  GET
Route::get('/guzzle2',"TextController@guzzle2");  //guzzle 测试  POST
Route::get('/uploads',"TextController@uploads");  //图文本
//小程序接口
Route::prefix('/api')->group(function (){
   Route::get('/test','Weixin\ApiController@test');
    Route::get('/goods_details','Weixin\ApiController@goods_details');//小程序详情页接收id
    Route::get('/add_fav','Weixin\ApiController@add_fav');//收藏
    Route::get('/no_fav','Weixin\ApiController@no_fav');//取消收藏
    Route::get('/goodsList','Weixin\ApiController@goodsList');//下拉刷新
});
Route::post('/wx/xcxlogin','Weixin\XcxController@login');  //微信登录小程序
Route::get('/wx/circulation','Weixin\XcxController@circulation');  //微信登录小程序
Route::get('/wx/cart','Weixin\XcxController@cart');  //微信小程序加入购物车
Route::get('/wx/cartlist','Weixin\XcxController@cartlist');  //购物车列表
Route::get('/wx/alldelete','Weixin\XcxController@alldelete');  //全部删除











