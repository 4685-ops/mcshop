<?php

namespace App\Services;

use App\Models\Brand;

class BrandServices extends BaseServices
{
    public function getBrandList($page, $limit, $sort, $order, $columns): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Brand::query();
        if (!empty($sort) && !empty($order)) {
            $query = $query->orderBy($sort, $order);
        }
        return $query->paginate($limit, $columns, 'page', $page);
    }

    public function getBrand($id)
    {
        return Brand::query()->where('deleted', 0)->where('id', $id)->first();
    }
}
