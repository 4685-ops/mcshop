<?php

namespace App\Http\Controllers\Wx;

use App\Inputs\PageInput;
use App\Models\goods\Goods;
use App\Models\promotion\GrouponRules;
use App\Services\goods\GoodsServices;
use App\Services\promotion\GrouponServices;

class GrouponController extends WxController
{
    public function list(): \Illuminate\Http\JsonResponse
    {
        $page = PageInput::new();
        $list = GrouponServices::getInstance()->getGrouponRules($page);

        $rules = collect($list->items());
        // 获取所有的商品id
        $goodsIds = $rules->pluck('goods_id')->toArray();

        // 获取所有的商品详情
        $goodsList = GoodsServices::getInstance()->getGoodsListByIds($goodsIds)
            ->keyBy('id');

        $voList = $rules->map(function (GrouponRules $rule) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($rule->goods_id);

            return [
                'id' => $goods->id,
                'name' => $goods->name,
                'brief' => $goods->brief,
                'picUrl' => $goods->pic_url,
                'counterPrice' => $goods->counter_price,
                'retailPrice' => $goods->retail_price,
                'grouponPrice' => bcsub($goods->retail_price, $rule->discount, 2),
                'grouponDiscount' => $rule->discount,
                'grouponMember' => $rule->discount_member,
                'expireTime' => $rule->expire_time
            ];
        });

        $list = $this->paginate($list, $voList);
        return $this->success($list);
    }

    public function test()
    {

        // 生成开团图片分享
        $rule = GrouponServices::getInstance()->getGrouponRulesById(3);

        GrouponServices::getInstance()->createGrouponShareImage($rule);


        //GrouponServices::getInstance()->checkGrouponValid($this->userId(),3,1);

        //  GrouponServices::getInstance()->openOrJoinGroupon($this->userId(),3,3); //开团
        //  GrouponServices::getInstance()->openOrJoinGroupon($this->userId(),3,3,1);//参团
    }
}
