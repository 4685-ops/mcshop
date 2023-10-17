<?php

namespace App\Enums;

class CouponEnums
{
    /**
     * 优惠券赠送类型，如果是0则通用券，用户领取；如果是1，则是注册赠券；如果是2，则是优惠券码兑换；
     * 优惠券类型
     */
    const TYPE_COMMON = 0;
    const TYPE_REGISTER = 1;
    const TYPE_CODE = 2;


    /**
     * 优惠券商品限制
     */
    const GOODS_TYPE_ALL = 0;
    const GOODS_TYPE_CATEGORY = 1;
    const GOODS_TYPE_ARRAY = 2;

    /**
     * 优惠券状态
     */
    const STATUS_NORMAL = 0;
    const STATUS_EXPIRED = 1;
    const STATUS_OUT = 2;

    /**
     * 优惠券时间类型
     */
    const TIME_TYPE_DAYS = 0;
    const TIME_TYPE_TIME = 1;
}
