<?php

namespace App\Services;

use App\Inputs\GoodsListInput;
use App\Models\Footprint;
use App\Models\Goods;
use App\Models\GoodsAttribute;
use App\Models\GoodsProduct;
use App\Models\GoodsSpecification;
use App\Models\Issue;

class GoodsServices extends BaseServices
{
    public function countGoodsOnSale(): int
    {
        return Goods::query()->where('deleted', 0)->where('is_on_sale', 1)->count('id');
    }

    public function getGoods($id)
    {
        return Goods::query()->where('deleted', 0)->find($id);
    }

    public function listGoods(GoodsListInput $input, $columns)
    {
        $query = $this->getQueryByGoodsFilter($input);
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }

        return $query->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    private function getQueryByGoodsFilter(GoodsListInput $input)
    {
        $query = Goods::query()->where('is_on_sale', 1);

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

    public function listL2Category(GoodsListInput $listInput)
    {
        $query = $this->getQueryByGoodsFilter($listInput);
        $categoryIds = $query->select(['category_id'])->pluck('category_id')->unique()->toArray();

        return CategoryServices::getInstance()->getL2ListByIds($categoryIds);
    }

    public function getGoodsAttribute($id)
    {
        return GoodsAttribute::query()->where('deleted', 0)->where('goods_id', $id)->get();
    }

    public function getGoodsSpecification($id)
    {
        $spec = GoodsSpecification::query()->where('deleted', 0)->where('goods_id', $id)->get()->groupBy('specification');
        return $spec->map(function ($v, $k) {
            return ['name' => $k, 'valueList' => $v];
        })->values();
    }

    public function getGoodsProduct($goodsId)
    {
        return GoodsProduct::query()->where('goods_id', $goodsId)->get();
    }

    public function getGoodsIssue($page = 1, $limit = 4)
    {
        return Issue::query()->forPage($page, $limit)->get();
    }

    public function saveFootprint($userId,$goodsId): bool
    {
        $footprint = new Footprint();
        $footprint->fill(['user_id' => $userId, 'goods_id' => $goodsId]);
        return $footprint->save();
    }
}
