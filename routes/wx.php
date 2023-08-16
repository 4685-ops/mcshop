<?php

use Illuminate\Support\Facades\Route;


# 用户模块-用户
Route::post('auth/regCaptcha', '\App\Http\Controllers\Wx\AuthController@regCaptcha');//发送注册验证码
Route::post('auth/captcha', '\App\Http\Controllers\Wx\AuthController@regCaptcha'); //验证码
Route::post('auth/register', '\App\Http\Controllers\Wx\AuthController@register');//账号注册
Route::post('auth/login', '\App\Http\Controllers\Wx\AuthController@login'); //账号登录
Route::get('auth/info', '\App\Http\Controllers\Wx\AuthController@info'); //用户信息
Route::get('auth/logout', '\App\Http\Controllers\Wx\AuthController@logout'); //账号登出
Route::post('auth/reset', '\App\Http\Controllers\Wx\AuthController@reset'); //账号密码重置
Route::get('auth/profile', '\App\Http\Controllers\Wx\AuthController@profile'); //账号修改

# 用户模块-地址
Route::get('address/list', '\App\Http\Controllers\Wx\AddressController@list'); //收货地址列表
Route::get('address/detail', '\App\Http\Controllers\Wx\AddressController@detail'); //收货地址详情
Route::get('address/save', '\App\Http\Controllers\Wx\AddressController@save'); //保存收货地址
Route::get('address/delete', '\App\Http\Controllers\Wx\AddressController@delete'); //删除收货地址

# 商品模块-类目
Route::get('catalog/index', '\App\Http\Controllers\Wx\CatalogController@index'); //分类目录全部分类数据接口
Route::get('catalog/current', '\App\Http\Controllers\Wx\CatalogController@current'); //分类目录当前分类数据接口

# 商品模块-品牌
Route::get('brand/list', '\App\Http\Controllers\Wx\BrandController@list'); //品牌列表
Route::get('brand/detail', '\App\Http\Controllers\Wx\BrandController@detail'); //品牌详情

# 商品模块-商品
Route::get('goods/count', '\App\Http\Controllers\Wx\GoodsController@count'); //统计商品总数
Route::get('goods/category', '\App\Http\Controllers\Wx\GoodsController@category'); //根据分类获取商品列表数据
Route::get('goods/list', '\App\Http\Controllers\Wx\GoodsController@list'); //获得商品列表
Route::get('goods/detail', '\App\Http\Controllers\Wx\GoodsController@detail'); //获得商品的详情


# 营销模块-优惠券
Route::get('coupon/list', '\App\Http\Controllers\Wx\CouponController@list'); //优惠券列表
Route::get('coupon/mylist', '\App\Http\Controllers\Wx\CouponController@mylist'); //我的优惠券列表
Route::get('coupon/receive', '\App\Http\Controllers\Wx\CouponController@receive'); //优惠券领取
#Route::any('coupon/selectlist', ''); //当前订单可用优惠券列表

# 营销模块-团购
Route::get('groupon/list', '\App\Http\Controllers\Wx\GrouponController@list'); //团购列表
Route::get('home/redirectShareUrl', '\App\Http\Controllers\Wx\HomeController@redirectShareUrl')->name('home.redirectShareUrl');
