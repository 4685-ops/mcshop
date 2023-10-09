<?php

namespace App\Http\Controllers\Wx;


use App\CodeResponse;
use App\Enums\Constant;
use App\Exceptions\BusinessException;
use App\Inputs\GoodsListInput;
use App\Services\CategoryServices;
use App\Services\GoodsServices;
use App\Services\SearchHistoryServices;

class GoodsController extends WxController
{
    protected $only = [];

    /**
     * 获取上架的商品总数
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(): \Illuminate\Http\JsonResponse
    {
        $count = GoodsServices::getInstance()->countGoodsOnSale();
        return $this->success($count);
    }

    /**
     * 获取一个类目下面的所有商品
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function category(): \Illuminate\Http\JsonResponse
    {
        $id = $this->verifyId('id');
        $cur = CategoryServices::getInstance()->getCategory($id);
        if (empty($cur)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL);
        }

        $parent = null;
        $children = null;
        if ($cur->pid == 0) {
            // 当前分类是一级分类
            $parent = $cur;
            $children = CategoryServices::getInstance()->getL2ListByPid($cur->id);
            $cur = $children->first() ?? $cur;
        } else {
            $parent = CategoryServices::getInstance()->getL1ById($cur->pid);
            $children = CategoryServices::getInstance()->getL2ListByPid($cur->pid);
        }

        return $this->success([
            'currentCategory' => $cur,
            'parentCategory' => $parent,
            'brotherCategory' => $children
        ]);
    }

    /**
     * @throws BusinessException
     */
    public function list()
    {
        $input = GoodsListInput::new();

        if ($this->isLogin() && !empty($keyword)) {
            // 保存搜索记录 搜索关键词来源
            SearchHistoryServices::getInstance()->save($this->userId(), $keyword, Constant::SEARCH_HISTORY_FROM_WX);
        }

    }

    public function detail()
    {

    }
}
