<?php

namespace App\Services;

use App\Enums\Constant;
use App\Models\goods\Comment;
use App\Services\user\UserServices;
use Illuminate\Support\Arr;

class CommentServices extends BaseService
{
    public function getCommentWithUserInfo($goodsId, $page = 1, $limit = 2): array
    {
        $comments = $this->getCommentByGoodsId($goodsId, $page, $limit);
        // 获取所有的用户id
        $userIds = Arr::pluck($comments->items(), 'user_id');
        $userIds = array_unique($userIds);
        $users = UserServices::getInstance()->getUsers($userIds)->keyBy('id');
        $data = collect($comments->items())->map(function (Comment $comment) use ($users) {
            // 获取单个用户信息
            $user = $users->get($comment->user_id);
            $comment = $comment->toArray();
            $comment['picList'] = $comment['picUrls'];
            $comment = Arr::only($comment, ['id', 'addTime', 'content', 'adminContent', 'picList']);
            $comment['nickname'] = $user->nickname ?? '';
            $comment['avatar'] = $user->avatar ?? '';
            return $comment;
        });
        return ['count' => $comments->total(), 'data' => $data];
    }

    private function getCommentByGoodsId($goodsId, $page, $limit, $sort = "add_time", $order = "desc"): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {

        return Comment::query()->where('value_id', $goodsId)
            ->where('type', Constant::COMMENT_TYPE_GOODS)
            ->orderBy($sort, $order)
            ->paginate($limit, ['*'], 'page', $page);
    }


}
