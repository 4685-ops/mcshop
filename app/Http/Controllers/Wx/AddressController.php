<?php

namespace App\Http\Controllers\Wx;

use App\Exceptions\BusinessException;
use App\Inputs\AddressInput;
use App\Services\AddressServices;

class AddressController extends WxController
{
    protected $only = [
        "list",
        "detail",
        "save",
        "delete",
    ];

    /**
     * 获取地址列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(): \Illuminate\Http\JsonResponse
    {
        $list = AddressServices::getInstance()->getAddressListByUserId($this->user()->id);
        return $this->successPaginate($list);
    }

    /**
     * 获取单个地址详情
     * @throws BusinessException
     */
    public function detail(): \Illuminate\Http\JsonResponse
    {
        $id = $this->verifyId('id', 0);

        $address = AddressServices::getInstance()->getAddress($this->userId(), $id);

        if (is_null($address)) {
            return $this->badArgumentValue();
        }

        return $this->success($address);
    }

    /**
     * 保存地址
     * @throws BusinessException
     */
    public function save(): \Illuminate\Http\JsonResponse
    {
        // 获取参数
        $input = AddressInput::new();
        $address = AddressServices::getInstance()->saveAddress($this->userId(), $input);
        return $this->success($address->id);
    }

    /**
     * 删除地址
     * @throws BusinessException
     */
    public function delete(): \Illuminate\Http\JsonResponse
    {
        $id = $this->verifyId('id', 0);
        AddressServices::getInstance()->delete($this->userId(), $id);
        return $this->success();
    }

}
