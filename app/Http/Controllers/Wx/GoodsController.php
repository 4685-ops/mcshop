<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Enums\Constant;
use App\Inputs\GoodsListInput;
use App\Models\Editor\CategoryModel;
use App\Models\goods\Category;
use App\Services\CollectServices;
use App\Services\CommentServices;
use App\Services\goods\BrandServices;
use App\Services\goods\CatalogServices;
use App\Services\goods\GoodsServices;
use App\Services\SearchHistoryServices;

class GoodsController extends WxController
{
    /**
     * 商品数量
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(): \Illuminate\Http\JsonResponse
    {
        $count = GoodsServices::getInstance()->countGoodsOnSale();
        return $this->success($count);
    }

    public function category()
    {
        //接收分类id
        $id = $this->verifyId('id', 0);
        //获取当前的分类信息
        $cur = CatalogServices::getInstance()->getCategory($id);
        //验证
        if (is_null($cur)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL);
        }

        $parent = null;
        $children = null;

        // 获取父分类 和所有的 子分类
        if ($cur->pid == 0) {
            $parent = $cur;
            $children = CatalogServices::getInstance()->getL2ListByPid($cur->id);
            $cur = $children->first() ?? $cur;
        } else {
            $parent = CatalogServices::getInstance()->getL1ById($cur->pid);
            $children = CatalogServices::getInstance()->getL2ListByPid($cur->pid);
        }

        return $this->success([
            'currentCategory' => $cur,
            'parentCategory' => $parent,
            'brotherCategory' => $children
        ]);
    }

    public function list()
    {
        $input = GoodsListInput::new();
        // 登录了并且关键词不为空 把搜索词保存下来

        if ($this->isLogin() && $input->keyword) {
            // 保存搜索信息
            SearchHistoryServices::getInstance()->save($this->userId(), $input->keyword, Constant::SEARCH_HISTORY_FROM_WX);
        }

        $columns = ['id', 'name', 'brief', 'pic_url', 'is_new', 'is_hot', 'counter_price', 'retail_price'];

        // 查询搜索出来的商品列表
        $goodsList = GoodsServices::getInstance()->getLists($input,$columns);

        $categoryList = GoodsServices::getInstance()->listL2Category($input);

        $goodsList = $this->paginate($goodsList);
        $goodsList['filterCategoryList'] = $categoryList;
        return $this->success($goodsList);
    }

    public function detail()
    {
        $id = $this->verifyId('id');
        // 获取商品详情
        $info = GoodsServices::getInstance()->getGoods($id);
        if (empty($info)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL);
        }

        // 获取商品属性
        $attr = GoodsServices::getInstance()->getGoodsAttribute($id);

        // 获取商品规格
        $spec = GoodsServices::getInstance()->getGoodsSpecification($id);

        // 获取sku的数据
        $product = GoodsServices::getInstance()->getGoodsProduct($id);

        // 获取问题列表
        $issue = GoodsServices::getInstance()->getGoodsIssue();

        // 获取品牌信息
        $brand = $info->brand_id ? BrandServices::getInstance()->getBrandById($info->brand_id) : (object) [];

        // 获取商品评论
        $comment = CommentServices::getInstance()->getCommentWithUserInfo($id);

        // 保留足迹 相当于历史记录
        $userHasCollect = 0;
        if ($this->isLogin()) {
            // 获取用户的收藏总数
            $userHasCollect = CollectServices::getInstance()->countByGoodsId($this->userId(), $id);
            GoodsServices::getInstance()->saveFootprint($this->userId(), $id);
        }

        // todo 团购信息
        // todo 系统配置

        return $this->success([
            'info' => $info,
            'userHasCollect' => $userHasCollect,
            'issue' => $issue,
            'comment' => $comment,
            'specificationList' => $spec,
            'productList' => $product,
            'attribute' => $attr,
            'brand' => $brand,
            'groupon' => [],
            'share' => false,
            'shareImage' => $info->share_url
        ]);
    }
}
