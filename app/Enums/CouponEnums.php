<?php

namespace App\Enums;

class CouponEnums
{
    /**
     *  优惠券赠送类型，如果是0则通用券，用户领取；如果是1，则是注册赠券；如果是2，则是优惠券码兑换；
     */
    const TYPE_COMMON = 0;
    const TYPE_REGISTER = 1;
    const TYPE_CODE = 2;


    /**
     * 优惠券商品限制 商品限制类型，如果0则全商品，如果是1则是类目限制，如果是2则是商品限制。
     */
    const GOODS_TYPE_ALL = 0;
    const GOODS_TYPE_CATEGORY = 1;
    const GOODS_TYPE_ARRAY = 2;

    /**
     * 优惠券状态，如果是0则是正常可用；如果是1则是过期; 如果是2则是下架。
     */
    const STATUS_NORMAL = 0;
    const STATUS_EXPIRED = 1;
    const STATUS_OUT = 2;

    /**
     * 优惠券时间类型 有效时间限制，如果是0，则基于领取时间的有效天数days；如果是1，则start_time和end_time是优惠券有效期；
     */
    const TIME_TYPE_DAYS = 0;
    const TIME_TYPE_TIME = 1;
}
