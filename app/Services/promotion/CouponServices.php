<?php

namespace App\Services\promotion;

use App\CodeResponse;
use App\Enums\CouponEnums;
use App\Exceptions\BusinessException;
use App\Inputs\PageInput;
use App\Models\promotion\Coupon;
use App\Models\promotion\CouponUser;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CouponServices extends BaseService
{
    public function list(PageInput $page, $columns): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Coupon::query()->where('type', CouponEnums::TYPE_COMMON)
            ->where('status', CouponEnums::STATUS_NORMAL)
            ->orderBy($page->sort, $page->order)
            ->paginate($page->limit, $columns, 'page', $page->page);
    }

    public function mylist($userId, $status, PageInput $page, $columns = ['*'])
    {
        return CouponUser::query()->where('user_id', $userId)
            ->when(!is_null($status), function (Builder $query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy($page->sort, $page->order)
            ->paginate($page->limit, $columns, 'page', $page->page);
    }


    public function getCoupons($couponIds)
    {
        return Coupon::query()->whereIn("id", $couponIds)->get();
    }

    public function getCoupon($couponId){
        return Coupon::query()->find($couponId);
    }

    /**
     * @throws BusinessException
     */
    public function receive($userId, $couponId)
    {
        // 判断优惠券id是否存在
        $coupon = CouponServices::getInstance()->getCoupon($couponId);
        if (is_null($coupon)) {
            $this->throwBusinessException(CodeResponse::PARAM_ILLEGAL);
        }

        /**
         * 判断优惠券数量
         */
        if ($coupon->total > 0) {
            // 查询已经领取的优惠券数量
            $fetchedCount = CouponServices::getInstance()->countCoupon($couponId);
            if ($fetchedCount >= $coupon->total) {
                $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT);
            }
        }

        /**
         * 判断用户领券限制数量，如果是0，则是不限制；默认是1，限领一张.
         */
        if ($coupon->limit > 0) {
            // 查询当前用户是否领取过该优惠券
            $userFetchedCount = CouponServices::getInstance()->countCouponByUserId($userId, $couponId);
            if ($userFetchedCount >= $coupon->limit) {
                $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT, '优惠券已经领取过');
            }
        }

        /**
         * 判断优惠券类型
         * 只有通用券才能领取
         * 优惠券赠送类型，如果是0则通用券，用户领取；如果是1，则是注册赠券；如果是2，则是优惠券码兑换；
         */
        if ($coupon->type != CouponEnums::TYPE_COMMON) {
            $this->throwBusinessException(CodeResponse::COUPON_RECEIVE_FAIL, '优惠券类型不支持');
        }

        /**
         * 判断优惠券状态 如果是已领完了
         */
        if ($coupon->status == CouponEnums::STATUS_OUT) {
            $this->throwBusinessException(CodeResponse::COUPON_EXCEED_LIMIT);
        }
        /**
         * 判断优惠券状态 是否过期了
         */
        if ($coupon->status == CouponEnums::STATUS_EXPIRED) {
            $this->throwBusinessException(CodeResponse::COUPON_RECEIVE_FAIL, '优惠券已经过期');
        }


        $couponUser = CouponUser::new();
        /**
         *  有效时间限制，如果是0，则基于领取时间的有效天数days；如果是1，则start_time和end_time是优惠券有效期；
         */
        if ($coupon->time_type == CouponEnums::TIME_TYPE_TIME) {
            // 表示优惠券 有开始时间和结束时间 这个时间段内有效
            $startTime = $coupon->start_time;
            $endTime = $coupon->end_time;
        } else {
            // 表示优惠券从当前时间算然后几天后过期
            $startTime = Carbon::now();
            $endTime = $startTime->copy()->addDays($coupon->days);
        }

        // 保存优惠券
        $couponUser->fill([
            'coupon_id' => $couponId,
            'user_id' => $userId,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);
        return $couponUser->save();
    }

    public function countCoupon($couponId): int
    {
        return Coupon::query()->where('id',$couponId)->count('id');
    }

    public function countCouponByUserId($userId,$couponId): int
    {
        return CouponUser::query()
            ->where('coupon_id',$couponId)
            ->where('user_id',$userId)
            ->count('id');
    }
}
