<?php

namespace App\Services\goods;

use App\Enums\Constant;
use App\Inputs\GoodsListInput;
use App\Models\goods\Footprint;
use App\Models\goods\Goods;
use App\Models\goods\GoodsAttribute;
use App\Models\goods\GoodsProduct;
use App\Models\goods\GoodsSpecification;
use App\Models\goods\Issue;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;

class GoodsServices extends BaseService
{
    public function countGoodsOnSale(): int
    {
        return Goods::query()->where("is_on_sale", Constant::IS_ON_SALE)->where("deleted", 0)->count('id');
    }

    public function listL2Category($input)
    {
        $query = $this->getQueryByGoodsFilter($input);
        // 获取所有的分类id
        $categoryIds = $query->select(['category_id'])->pluck('category_id')->unique()->toArray();

        return CatalogServices::getInstance()->getL2ListByIds($categoryIds);
    }

    public function getLists(GoodsListInput $input, $columns = ["*"])
    {
        $query = $this->getQueryByGoodsFilter($input);
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }

        return $query->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoods($id)
    {
        return Goods::query()->where("deleted", 0)->find($id);
    }

    public function getGoodsAttribute($goods_id)
    {
        return GoodsAttribute::query()->where("goods_id", $goods_id)->get();
    }

    public function getGoodsSpecification($goods_id)
    {
        // 按商品规格名称分组
        /**
         * array:2 [▼
         *       "规格" => array:2 [▼
         *           0 => array:8 [▶]
         *           1 => array:8 [▶]
         *       ]
         *       "颜色" => array:3 [▼
         *           0 => array:8 [▶]
         *           1 => array:8 [▶]
         *           2 => array:8 [▶]
         *       ]
         *   ]
         */
        $specData = GoodsSpecification::query()->where("goods_id", $goods_id)->get()->groupBy('specification');

        /**
         * 返回的需要这样的数据
         * array:2 [▼
         *      0 => array:2 [▼
         *          "name" => "规格"
         *          "valueList" => []
         *      ]
         *      1 => array:2 [▼
         *          "name" => "颜色"
         *          "valueList" => []
         *      ]
         *  ]
         */
        return $specData->map(function ($v, $k) {
            return ['name' => $k, 'valueList' => $v];
        })->values();
    }

    public function getGoodsProduct($goods_id)
    {
        return GoodsProduct::query()->where("goods_id", $goods_id)->get();
    }

    public function getGoodsIssue(int $page = 1, int $limit = 4)
    {
        return Issue::query()->forPage($page, $limit)->get();
    }

    public function saveFootprint($userId, $goodsId): bool
    {
        $footprint = new Footprint();
        $footprint->fill(['user_id' => $userId, 'goods_id' => $goodsId]);
        return $footprint->save();
    }

    private function getQueryByGoodsFilter(GoodsListInput $input)
    {
        $query = Goods::query()->where('is_on_sale', Constant::IS_ON_SALE);

        if (!empty($input->brandId)) {
            $query = $query->where('brand_id', $input->brandId);
        }
        if (!is_null($input->isNew)) {
            $query = $query->where('is_new', $input->isNew);
        }
        if (!is_null($input->isHot)) {
            $query = $query->where('is_hot', $input->isHot);
        }
        if (!empty($input->keyword)) {
            $query = $query->where(function (Builder $query) use ($input) {
                $query->where('keywords', 'like', "%$input->keyword%")
                    ->orWhere('name', 'like', "%$input->keyword%");
            });
        }
        return $query;
    }

    public function getGoodsListByIds($goodIds)
    {
        return Goods::query()->where("deleted", 0)
            ->whereIn("id",$goodIds)
            ->get();
    }

}
