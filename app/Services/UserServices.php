<?php

namespace App\Services;

use App\Models\User;

class UserServices
{
    public function getByUsername(string $name)
    {
        return User::query()->where('username', $name)->where('deleted', 0)->first();
    }


    public function getByMobile(string $mobile)
    {
        return User::query()->where('mobile', $mobile)->where('deleted', 0)->first();
    }
}
