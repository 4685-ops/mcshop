<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Services\CategoryServices;
use Illuminate\Http\Request;

class CatalogController extends WxController
{
    public function index(Request $request)
    {
        $id = $request->input('id', 0);
        // 1.先获取所有的L1分类
        $l1List = CategoryServices::getInstance()->getL1List();
        // 2.当前分类：如果传递了id就获取 不传递获取第一个
        if (empty($id)) {
            $current = $l1List->first();
        } else {
            $current = $l1List->where('id', $id)->first();
        }
        // 3.获取当前分类下的所有子分类
        $l2List = [];
        if (!is_null($current)){
            $l2List = CategoryServices::getInstance()->getL2ListByPid($current->id);
        }

        return $this->success(
            [
                'categoryList' => $l1List,
                'currentCategory' => $current,
                'currentSubCategory' => $l2List
            ]
        );
    }

    public function current(Request $request)
    {
        //1.接收参数
        $id = $request->input('id', 0);
        //2.验证参数
        if (empty($id)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL);
        }
        //3.获取当前的分类信息
        $category = CategoryServices::getInstance()->getL1ById($id);
        //4.验证当前的分类信息

        if (is_null($category)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL);
        }
        //5.获取当前分类的所有子分类
        $l2List = CategoryServices::getInstance()->getL2ListByPid($category->id);
        //6.返回数据
        return $this->success(
            [
                'currentCategory' => $category,
                'currentSubCategory' => $l2List->toArray()
            ]
        );
    }
}
