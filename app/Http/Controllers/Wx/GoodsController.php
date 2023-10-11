<?php

namespace App\Http\Controllers\Wx;


use App\CodeResponse;
use App\Enums\Constant;
use App\Exceptions\BusinessException;
use App\Inputs\GoodsListInput;
use App\Services\BrandServices;
use App\Services\CategoryServices;
use App\Services\CollectServices;
use App\Services\CommentServices;
use App\Services\GoodsServices;
use App\Services\SearchHistoryServices;
use Illuminate\Http\Request;

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

        $columns = ['id', 'name', 'brief', 'pic_url', 'is_new', 'is_hot', 'counter_price', 'retail_price'];
        $goodsList = GoodsServices::getInstance()->listGoods($input, $columns);


        $categoryList = GoodsServices::getInstance()->listL2Category($input);

        $goodsList = $this->paginate($goodsList);
        $goodsList['filterCategoryList'] = $categoryList;
        return $this->success($goodsList);

    }

    public function detail()
    {
        $id = $this->verifyId('id');
        $info = GoodsServices::getInstance()->getGoods($id);
        if (empty($info)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL);
        }

        // 属性
        $attr = GoodsServices::getInstance()->getGoodsAttribute($id);

        //规格
        $spec = GoodsServices::getInstance()->getGoodsSpecification($id);

        //商品信息
        $product = GoodsServices::getInstance()->getGoodsProduct($id);

        //注意事项
        $issue = GoodsServices::getInstance()->getGoodsIssue();

        //品牌信息
        $brand = $info->brand_id ? BrandServices::getInstance()->getBrand($info->brand_id) : (object)[];

        //评论
        $comment = CommentServices::getInstance()->getCommentWithUserInfo($id);

        $userHasCollect = 0;
        if ($this->isLogin()) {
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
