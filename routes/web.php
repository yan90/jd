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
//});
Route::get('/register','index\LoginController@register'); //        //前台注册视图
Route::post('/index/home','index\LoginController@home');              //注册执行
Route::get('/user/active','index\LoginController@active');             //激活用户
Route::get('login','index\LoginController@login');             //前台登录视图
Route::post('/index/logindo','index\LoginController@logindo');           //登录执行
Route::get('/logout','index\LoginController@logout');                //退出
Route::get('/','index\IndexController@index');                 //前台首页
Route::get('/index/seckill','index\IndexController@seckill');        //秒杀
Route::get('/index/cart','index\IndexController@cart');                    //加入购物车
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









