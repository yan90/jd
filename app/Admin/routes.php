<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    //用户管理
    $router->resource('users', UserController::class);
    //商品管理
    $router->resource('goods', GoodsController::class);
    //分类管理
    $router->resource('cate', CateController::class);
});
