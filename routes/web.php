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

Route::get('/', function () {
    return view('welcome');
});
// //前台注册视图
Route::get('/index/register','index\LoginController@register');
//注册执行
Route::post('/index/home','index\LoginController@home');
//激活用户
Route::get('/user/active','index\LoginController@active');

//前台登录视图
Route::get('/index/login','index\LoginController@login');
//登录执行
Route::post('/index/logindo','index\LoginController@logindo');
//退出
Route::get('/index/logout','index\LoginController@logout');
//前台首页
Route::get('/index/index','index\IndexController@index');
//秒杀
Route::get('/index/seckill','index\IndexController@seckill');
//购物车
Route::get('/index/cart','index\IndexController@cart');
//商品详情
Route::get('/index/particulars','index\IndexController@particulars');

