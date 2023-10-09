<?php

namespace App\Services;

use App\Models\Goods;

class GoodsServices extends BaseServices
{
    public function countGoodsOnSale(): int
    {
        return Goods::query()->where('deleted', 0)->where('is_on_sale', 1)->count('id');
    }
}
