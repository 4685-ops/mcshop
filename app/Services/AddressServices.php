<?php

namespace App\Services;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Inputs\AddressInput;
use App\Models\Address;

class AddressServices extends BaseServices
{
    public function getAddressListByUserId($userId)
    {
        return Address::query()->where('deleted', 0)->where('user_id', $userId)->get();
    }

    public function getAddress($userId, $addressId)
    {
        return Address::query()->where('deleted', 0)->where('user_id', $userId)
            ->where('id', $addressId)->first();
    }

    public function saveAddress($userId, AddressInput $input)
    {
        // 判断是添加还是保存的
        if (!is_null($input->id)) {
            $address = AddressServices::getInstance()->getAddress($userId, $input->id);
        } else {
            $address = Address::new();
            $address->user_id = $userId;
        }

        // 如果当前的地址是默认的 需要把之前都默认改为非默认
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
     * @param $userId
     * @return bool|int
     */
    public function resetDefault($userId)
    {
        return Address::query()->where('user_id', $userId)->where('is_default', 1)->update(['is_default' => 0]);
    }

    /**
     * @throws BusinessException
     */
    public function delete($userId, $addressId)
    {
        $address = $this->getAddress($userId, $addressId);
        if (is_null($address)) {
            $this->throwBusinessException(CodeResponse::PARAM_VALUE_ILLEGAL);
        }
        return $address->delete();
    }
}
