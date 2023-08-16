<?php

namespace App\Http\Controllers\Wx;

use App\Inputs\PageInput;
use App\Models\promotion\Coupon;
use App\Models\promotion\CouponUser;
use App\Services\promotion\CouponServices;

class CouponController extends WxController
{
    /**
     * 获取优惠券列表
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function list(): \Illuminate\Http\JsonResponse
    {
        $page = PageInput::new();
        $columns = ['id', 'name', 'desc', 'tag', 'discount', 'min', 'days', 'start_time', 'end_time'];
        $list = CouponServices::getInstance()->list($page, $columns);
        return $this->successPaginate($list);
    }

    /**
     * 我的优惠券
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function mylist()
    {
        $status = $this->verifyInteger('status');
        $page = PageInput::new();
        $list = CouponServices::getInstance()->mylist($this->userId(), $status, $page);

        // 获取所有的coupon_id
        $couponUserList = collect($list->items());
        $couponIds = $couponUserList->pluck('coupon_id')->toArray();

        // 获取优惠券信息
        $coupons = CouponServices::getInstance()->getCoupons($couponIds)->keyBy('id');

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
     * @throws \App\Exceptions\BusinessException
     */
    public function receive()
    {
        $couponId = $this->verifyId('couponId', 0);
        CouponServices::getInstance()->receive($this->userId(), $couponId);
        return $this->success();
    }
}
