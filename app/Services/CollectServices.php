<?php

namespace App\Services;

use App\Enums\Constant;
use App\Models\goods\Collect;

class CollectServices extends BaseService
{
    public function countByGoodsId($userId, $goodsId): int
    {
        return Collect::query()->where('user_id', $userId)
            ->where('value_id', $goodsId)
            ->where('type', Constant::COLLECT_TYPE_GOODS)
            ->count('id');
    }
}
