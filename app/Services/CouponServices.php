<?php

namespace App\Services;

use App\CodeResponse;
use App\Enums\CouponEnums;
use App\Exceptions\BusinessException;
use App\Models\Coupon;
use App\Models\CouponUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CouponServices extends BaseServices
{
    public function list(\App\Inputs\Input $page, array $columns)
    {
        return Coupon::query()->where('type', CouponEnums::TYPE_COMMON)
            ->where('status', CouponEnums::STATUS_NORMAL)
            ->orderBy($page->sort, $page->order)
            ->paginate($page->limit, $columns, 'page', $page->page);
    }

    public function mylist(bool $userId, $status, \App\Inputs\Input $page, $columns = ['*'])
    {
        return CouponUser::query()->where('user_id', $userId)
            ->when(!is_null($status), function (Builder $query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy($page->sort, $page->order)
            ->paginate($page->limit, $columns, 'page', $page->page);
    }

    public function getCoupons(array $ids, $columns = ['*'])
    {
        return Coupon::query()->whereIn('id', $ids)
            ->get($columns);
    }

    /**
     * @throws BusinessException
     */
    public function receive($userId, $couponId)
    {
        // 获取优惠券信息
        $coupon = CouponServices::getInstance()->getCoupon($couponId);
        if (is_null($coupon)) {
            $this->throwBusinessException(CodeResponse::PARAM_ILLEGAL);
        }

        // 判断优惠券是否已领完
        if ($coupon->total > 0) {
            $fetchedCount = CouponServices::getInstance()->countCoupon($couponId);
            if ($fetchedCount >= $coupon->total) {
                $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT);
            }
        }

        // 判断优惠券是否已经领取过
        if ($coupon->limit > 0) {
            $userFetchedCount = CouponServices::getInstance()->countCouponByUserId($userId, $couponId);
            if ($userFetchedCount >= $coupon->limit) {
                $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT, '优惠券已经领取过');
            }
        }

        // 优惠券类型不支持 如果是0则通用券，用户领取；如果是1，则是注册赠券；如果是2，则是优惠券码兑换；
        if ($coupon->type != CouponEnums::TYPE_COMMON) {
            $this->throwBusinessException(CodeResponse::COUPON_RECEIVE_FAIL, '优惠券类型不支持');
        }

        // 优惠券已领完 优惠券状态，如果是0则是正常可用；如果是1则是过期; 如果是2则是下架。
        if ($coupon->status == CouponEnums::STATUS_OUT) {
            $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT);
        }

        // 优惠券已经过期 优惠券状态，如果是0则是正常可用；如果是1则是过期; 如果是2则是下架。
        if ($coupon->status == CouponEnums::STATUS_EXPIRED) {
            $this->throwBusinessException(CodeResponse::COUPON_RECEIVE_FAIL, '优惠券已经过期');
        }

        $couponUser = CouponUser::new();
        // 优惠券时间类型 有效时间限制，如果是0，则基于领取时间的有效天数days；   如果是1，则start_time和end_time是优惠券有效期；
        if ($coupon->time_type == CouponEnums::TIME_TYPE_TIME) {
            $startTime = $coupon->start_time;
            $endTime = $coupon->end_time;
        } else {
            $startTime = Carbon::now();
            $endTime = $startTime->copy()->addDays($coupon->days);
        }

        $couponUser->fill([
            'coupon_id' => $couponId,
            'user_id' => $userId,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);
        return $couponUser->save();
    }

    public function getCoupon($id, $columns = ['*'])
    {
        return Coupon::query()->find($id, $columns);
    }

    public function countCoupon($couponId)
    {
        return CouponUser::query()->where('coupon_id', $couponId)
            ->count('id');
    }

    public function countCouponByUserId($userId, $couponId)
    {
        return CouponUser::query()->where('coupon_id', $couponId)
            ->where('user_id', $userId)
            ->count('id');
    }
}
