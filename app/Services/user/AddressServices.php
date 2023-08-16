<?php

namespace App\Services\user;


use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Inputs\AddressInput;
use App\Models\user\Address;
use App\Models\user\User;
use App\Services\BaseService;

class AddressServices extends BaseService
{
    public function getAddressListByUserId(int $userId)
    {
        return Address::query()->where('user_id', $userId)
            ->get();
    }

    /**
     * 获取用户地址
     * @param int $userId
     * @param int $addressId
     * @return \App\Models\BaseModel|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getAddressById(int $userId, int $addressId)
    {
        return Address::query()->where('id', $addressId)->where('user_id', $userId)->where('deleted', 0)->first();
    }

    public function saveAddress(int $userId, AddressInput $input)
    {
        if (!is_null($input->id)) {
            $address = AddressServices::getInstance()->getAddressById($userId, $input->id);
        } else {
            $address = Address::new();
            $address->user_id = $userId;
        }

        // 如果默认地址设置了 要在用户表中更新
        if ($input->isDefault) {
            $this->resetDefault($userId);
        }

        $address->address_detail = $input->addressDetail;
        $address->area_code = $input->areaCode;
        $address->city = $input->city;
        $address->county = $input->county;
        $address->is_default = $input->isDefault;
        $address->name = $input->name;
        $address->postal_code = $input->postalCode;
        $address->province = $input->province;
        $address->tel = $input->tel;
        $address->save();
        return $address;
    }


    /**
     * @throws BusinessException
     */
    public function delAddress(int $userId, int $addressId)
    {
        $address = Address::query()->where('id', $addressId)->where('user_id', $userId)
            ->where('deleted', 0)->first();
        if (is_null($address)) {
            $this->throwBusinessException(CodeResponse::PARAM_ILLEGAL);
        }
        $address->deleted = 1;
        $address->save();

        return $address;
    }

    /**
     * 更新掉之前的默认地址
     *
     * @param int $userId
     * @return bool|int
     */
    private function resetDefault(int $userId)
    {
        return Address::query()->where('user_id', $userId)->where('is_default', 1)->update(['is_default' => 0]);
    }


}
