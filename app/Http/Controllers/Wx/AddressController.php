<?php

namespace App\Http\Controllers\Wx;

use App\Exceptions\BusinessException;
use App\Inputs\AddressInput;
use App\Services\user\AddressServices;

class AddressController extends WxController
{
    // 通过用户id获取个人的地址列表
    public function list(){
        $list = AddressServices::getInstance()->getAddressListByUserId($this->user()->id);
        return $this->successPaginate($list);
    }

    /**
     * @throws BusinessException
     */
    public function detail(){
        $id = $this->verifyId('id', 0);
        $address = AddressServices::getInstance()->getAddressById($this->user()->id,$id);
        if (empty($address)) {
            return $this->badArgumentValue();
        }
        return $this->success($address);
    }

    public function save(){
        $input = AddressInput::new();
        $address = AddressServices::getInstance()->saveAddress($this->userId(), $input);
        return $this->success($address->id);
    }

    /**
     * @throws BusinessException
     */
    public function delete(): \Illuminate\Http\JsonResponse
    {
        $id = $this->verifyId('id', 0);
        $address = AddressServices::getInstance()->delAddress($this->user()->id,$id);
        return $this->success($address->id);
    }
}
