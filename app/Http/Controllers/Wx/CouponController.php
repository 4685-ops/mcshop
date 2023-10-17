<?php

namespace App\Http\Controllers\Wx;

use App\Exceptions\BusinessException;
use App\Inputs\PageInput;
use App\Models\Coupon;
use App\Models\CouponUser;
use App\Services\CouponServices;

class CouponController extends WxController
{
    protected $except = ['list'];


    /**
     * 优惠券列表
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function list()
    {
        $page = PageInput::new();
        $columns = ['id', 'name', 'desc', 'tag', 'discount', 'min', 'days', 'start_time', 'end_time'];
        $list = CouponServices::getInstance()->list($page, $columns);
        return $this->successPaginate($list);
    }


    /**
     * 我的优惠券列表
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function mylist()
    {
        // 获取当前领过的优惠券
        $status = $this->verifyInteger('status');
        $page = PageInput::new();
        $list = CouponServices::getInstance()->mylist($this->userId(), $status, $page);


        // 获取优惠券详情
        $couponUserList = collect($list->items());
        $couponIds = $couponUserList->pluck('coupon_id')->toArray();
        $coupons = CouponServices::getInstance()->getCoupons($couponIds)->keyBy('id');

        // 组合数据
        $mylist = $couponUserList->map(function (CouponUser $item) use ($coupons) {
            /** @var Coupon $coupon */
            $coupon = $coupons->get($item->coupon_id);
            return [
                'id' => $item->id,
                'cid' => $coupon->id,
                'name' => $coupon->name,
                'desc' => $coupon->desc,
                'tag' => $coupon->tag,
                'min' => $coupon->min,
                'discount' => $coupon->discount,
                'startTime' => $item->start_time,
                'endTime' => $item->end_time,
                'available' => false
            ];
        });
        $list = $this->paginate($list, $mylist);
        return $this->success($list);
    }

    /**
     * 领取优惠券
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     * @throws BusinessException
     */
    public function receive(): \Illuminate\Http\JsonResponse
    {
        $couponId = $this->verifyId('couponId', 0);
        CouponServices::getInstance()->receive($this->userId(), $couponId);
        return $this->success();
    }
}
