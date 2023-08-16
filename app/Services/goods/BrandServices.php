<?php

namespace App\Services\goods;


use App\Inputs\PageInput;
use App\Models\goods\Brand;
use App\Models\goods\Category;
use App\Services\BaseService;

class BrandServices extends BaseService
{
    public function getBrandList(PageInput $input, $columns): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Brand::query();
        if (!empty($input->sort) && !empty($input->order)) {
            $query = $query->orderBy($input->sort, $input->order);
        }
        return $query->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getBrandById($id)
    {
        return Brand::query()->where("deleted", 0)->find($id);
    }
}
