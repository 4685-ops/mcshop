<?php

namespace App\Services;

use App\Models\Category;

class CategoryServices extends BaseServices
{
    public function getL1List()
    {
        return Category::query()->where('deleted', 0)->where('level', 'L1')->get();
    }

    public function getL2ListByPid($id)
    {
        return Category::query()->where('deleted', 0)->where('level', 'L2')->where('pid', $id)->get();
    }

    public function getL1ById($id)
    {
        return Category::query()->where('deleted', 0)->where('level', 'L1')->where('id', $id)->first();
    }
}
