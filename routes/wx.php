<?php

use App\Http\Controllers\Wx\BrandController;
use App\Http\Controllers\Wx\CouponController;
use App\Http\Controllers\Wx\GoodsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Wx\AddressController;
use App\Http\Controllers\Wx\CatalogController;
use App\Http\Controllers\Wx\AuthController;

//# 用户模块-用户
Route::post('auth/register', [AuthController::class, 'register']);//账号注册
Route::post('auth/regCaptcha', [AuthController::class, 'regCaptcha']);//注册验证码
Route::post('auth/login', [AuthController::class, 'login']);//登录
Route::get('auth/info', [AuthController::class, 'info']); //用户信息
Route::post('auth/logout', [AuthController::class, 'logout']); //账号登出
Route::post('auth/reset', [AuthController::class, 'reset']); //账号密码重置
Route::post('auth/captcha', [AuthController::class, 'regCaptcha']); //验证码
Route::post('auth/profile', [AuthController::class, 'profile']); //账号修改

//# 用户模块-地址
Route::get('address/list', [AddressController::class, 'list']); //收货地址列表
Route::get('address/detail', [AddressController::class, 'detail']); //收货地址详情
Route::post('address/save', [AddressController::class, 'save']); //保存收货地址
Route::post('address/delete', [AddressController::class, 'delete']); //删除收货地址


//# 商品模块-类目
Route::get('catalog/index', [CatalogController::class, 'index']); //分类目录全部分类数据接口
Route::get('catalog/current', [CatalogController::class, 'current']); //分类目录当前分类数据接口


//# 商品模块-品牌
Route::get('brand/list', [BrandController::class, 'list']); //品牌列表
Route::get('brand/detail', [BrandController::class, 'detail']); //品牌详情


//# 商品模块-商品
Route::get('goods/count', [GoodsController::class, 'count']); //统计商品总数
Route::get('goods/category', [GoodsController::class, 'category']); //根据分类获取商品列表数据
Route::get('goods/list', [GoodsController::class, 'list']); //获得商品列表
Route::get('goods/detail', [GoodsController::class, 'detail']); //获得商品的详情

//# 营销模块-优惠券
Route::get('coupon/list', [CouponController::class, 'list']); //优惠券列表
Route::get('coupon/mylist', [CouponController::class, 'mylist']); //我的优惠券列表
Route::post('coupon/receive', [CouponController::class, 'receive']); //优惠券领取
//Route::any('coupon/selectlist', ''); //当前订单可用优惠券列表
//
//# 营销模块-团购
//Route::get('groupon/list', 'GrouponController@list'); //团购列表
//
//Route::get('home/redirectShareUrl', 'HomeController@redirectShareUrl')->name('home.redirectShareUrl');
//
//# 订单模块-购物车
//Route::post('cart/add', 'CartController@add'); // 添加商品到购物车
//Route::get('cart/goodscount', 'CartController@goodscount'); // 获取购物车商品件数
//Route::post('cart/update', 'CartController@update'); // 更新购物车的商品的数量
//Route::post('cart/delete', 'CartController@delete'); // 删除购物车的商品
//Route::post('cart/checked', 'CartController@checked'); // 选择或取消选择商品
//Route::post('cart/fastadd', 'CartController@fastadd'); // 立即购买商品
//Route::get('cart/index', 'CartController@index'); //获取购物车的数据
//Route::get('cart/checkout', 'CartController@checkout'); // 下单前信息确认
//
//# 订单模块-订单
//Route::post('order/submit', 'OrderController@submit'); // 提交订单
//Route::post('order/cancel', 'OrderController@cancel'); //取消订单
//Route::post('order/refund', 'OrderController@refund'); //退款取消订单
//Route::post('order/delete', 'OrderController@delete'); //删除订单
//Route::post('order/confirm', 'OrderController@confirm'); //确认收货
////Route::any('order/prepay', ''); // 订单的预支付会话 - jsapi
//Route::post('order/h5pay', 'OrderController@h5pay'); // 微信支付 - h5
//Route::post('order/wxNotify', 'OrderController@wxNotify'); // 微信支付回调
//Route::post('order/h5alipay', 'OrderController@h5alipay'); // 支付宝支付 - h5
//Route::post('order/alipayNotify', 'OrderController@alipayNotify'); // 支付宝支付回调
//Route::get('order/alipayReturn', 'OrderController@alipayReturn'); // 支付宝支付回调
//Route::any('order/list', 'OrderController@list'); //订单列表
//Route::get('order/detail', 'OrderController@detail'); //订单详情
//Route::get('order/detail', [OrderController::class, 'detail']); //订单详情


//Route::any('home/index', ''); //首页数据接口

//Route::any('collect/list', ''); //收藏列表
//Route::any('collect/addordelete', ''); //添加或取消收藏
//Route::any('topic/list', ''); //专题列表
//Route::any('topic/detail', ''); //专题详情
//Route::any('topic/related', ''); //相关专题

//Route::any('feedback/submit', ''); //添加反馈


//Route::any('user/index', ''); //个人页面用户相关信息
//Route::any('issue/list', ''); //帮助信息
