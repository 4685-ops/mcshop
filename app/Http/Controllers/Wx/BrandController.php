<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Inputs\PageInput;
use App\Services\goods\BrandServices;

class BrandController extends WxController
{
    protected $except = ["lists", "detail"];

    public function list()
    {
        $inputs = PageInput::new();
        $columns = ['id', 'name', 'desc', 'pic_url', 'floor_price'];
        $list = BrandServices::getInstance()->getBrandList($inputs, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyId('id',0);

        $columns = ['id', 'name', 'desc', 'pic_url', 'floor_price'];
        $detail = BrandServices::getInstance()->getBrandById($id, $columns);
        if (is_null($detail)){
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL);
        }
        return $this->success($detail);
    }
}
