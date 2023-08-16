<?php

namespace App\Services\goods;


use App\Models\goods\Category;
use App\Services\BaseService;

class CatalogServices extends BaseService
{
    public function getL1List()
    {
        return Category::query()->where('level', 'L1')->where('deleted', 0)
            ->get();
    }

    public function getL1ById(int $id)
    {
        return Category::query()->where('level', 'L1')
            ->where('deleted', 0)
            ->find($id);
    }

    public function getCategory(int $id)
    {
        return Category::query()
            ->where('deleted', 0)
            ->find($id);
    }

    public function getL2ListByPid(int $id)
    {
        return Category::query()->where('level', 'L2')->where('pid', $id)->where('deleted', 0)
            ->get();
    }

    public function getL2ListByIds( $ids){
        if (empty($ids)) {
            return collect([]);
        }

        return Category::query()
            ->whereIn('id', $ids)
            ->where('deleted', 0)
            ->where('level', 'L2')
            ->get();

    }
}
